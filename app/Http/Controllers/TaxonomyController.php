<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxonomyController extends Controller
{
    //
    public function listEveryCategory(Request $request) {
        $keyword = $request->get('key') ? $request->get('key') : '';
        return Taxonomy::where('slug', 'like', "%{$keyword}%")->get()->toArray();
    }

    public function listAvailableCategories(Request $request) {
        $alltax = $this->listEveryCategory($request);

        $avail_taxes = array();

        foreach ($alltax as $taxonomy) {
            if (!$taxonomy['is_restricted']) {
                $avail_taxes[] = $taxonomy;
            }
            else {
                $user = $request->user();
                if ($this->canAccessTaxonomy($user, $taxonomy['slug'])) {
                    $avail_taxes[] = $taxonomy;
                }
            }
        }
        return $avail_taxes;

    }

    //All categories with keywords that are free from restriction
    public function listFreeCategories(Request $request) {
        $keyword = $request->get('key') ? $request->get('key') : '';
        return Taxonomy::where('slug', 'like', "%{$keyword}%")
            ->where('is_restricted', '=', 0)
            ->get()
            ->toArray();
    }

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

    protected function canAccessTaxonomy($user, $taxonomy_slug) {
        return $user->can('access_' . $taxonomy_slug . '_taxonomy');
    }
}
