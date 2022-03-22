<?php

namespace App\Traits;

use App\Models\Taxonomy;
use Illuminate\Http\Request;

trait TaxonomyListingTrait {
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
}