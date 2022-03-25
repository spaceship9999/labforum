<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use App\Traits\ResponseTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\TaxonomyListingTrait;
use PHPUnit\Exception;
use Throwable;

class TaxonomyController extends Controller
{

   use TaxonomyListingTrait, ResponseTraits;

    public function listCategories(Request $request) {
        if (Auth::guard('api')->check()) {
            Auth::loginUsingId(Auth::guard('api')->user()->id);
            if ($request->user()->can('admin_site')) {
                return $this->listEveryCategory($request);
            }
            return $this->listAvailableCategories($request);
        }
        return $this->listFreeCategories($request);
    }

    public function getFeaturedCategories(Request $request) {
        $all_feat_cat = Taxonomy::where('is_featured', '=', 1)->get()->toArray();
        if (Auth::guard('api')->check()) {
            Auth::loginUsingId(Auth::guard('api')->user()->id);
            if ($request->user()->can('admin_site')) {
                return $all_feat_cat;
            }

            //No admin rights, only logged in
            $avail_cat = array();
            foreach ($all_feat_cat as $cat) {
                if ($this->canAccessTaxonomy($request->user(), $cat['slug'])) {
                    $avail_cat[] = $cat;
                }
            }
            return $avail_cat;
        }
        return Taxonomy::where('is_featured', '=', 1)
            ->where('is_restricted', '=', 0)
            ->get()->toArray();
    }

    public function isTaxonomyRestricted($id) {
        $is_restricted = Taxonomy::whereId($id)
            ->pluck('is_restricted')->toArray();
        return !!$is_restricted[0];
    }

    protected function canAccessTaxonomy($user, $taxonomy_slug) {
        return $user->can('access_' . $taxonomy_slug . '_taxonomy');
    }

    public function getPostsInTaxonomy(Request $request, $id, $pagination = 20) {
        $array = Taxonomy::find($id)->posts()->paginate($pagination);


        return array(
            'data' => $array->items(),
            'current_page' => $array->currentPage(),
            'last_page' => $array->lastPage(),
            'num_posts' => $array->total(),
            'post_per_page' => $pagination,
        );
    }

    public function getTaxonomyBySlug(Request $request, $slug) {
        $pagination = !empty($request->get('post_per_page')) && is_numeric($request->get('post_per_page')) ?
            (int) $request->get('post_per_page') : 20;

        try {
            $id_arr = Taxonomy::where('slug', $slug)
                ->pluck('id')->toArray();
            $id = $id_arr[0];
        }
        catch (Throwable $e) {
            return $this->pageNotFound();
        }

        if (Auth::guard('api')->check()) {
            if ($this->isTaxonomyRestricted($id)) {
                if (!$this->canAccessTaxonomy($request->user(), $slug) && !$request->user()->can('admin_site')) {
                    return $this->noAccessRightResponse();
                }
            }
        }
        else if ($this->isTaxonomyRestricted($id)) {
            //Guest can't see the taxonomy
            return $this->noAccessRightResponse();
        }

        // Show Taxonomy
        $taxonomy = Taxonomy::where('id', '=', $id)->get()->toArray()[0];
        $taxonomy['posts'] =  $this->getPostsInTaxonomy($request, $id, $pagination);

        return $taxonomy;
    }
}
