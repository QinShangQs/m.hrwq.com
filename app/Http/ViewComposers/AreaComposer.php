<?php

namespace App\Http\ViewComposers;

use App\Models\Area;
use Illuminate\Contracts\View\View;
use Auth, Cache;

class AreaComposer
{
    public function __construct()
    {

    }

    public function compose(View $view)
    {
        $view->with('area', Cache::get('area', function(){


            $levelOne = Area::where('area_deep', 1)->orderBy('area_id')->get();
            $levelTwo = Area::where('area_deep', 2)->orderBy('area_id')->get();
            $levelThree = Area::where('area_deep', 3)->orderBy('area_id')->get();
            //重新组织一级、二级数组
            $children = [];
            if(!empty($levelTwo)) {
                foreach ($levelTwo as $child) {
                    $children[$child->area_id] = $child->toArray();
                }
            }

            $parents = [];
            if(!empty($levelOne)) {
                foreach ($levelOne as $parent) {
                    $parents[$parent->area_id] = $parent->toArray();
                }
            }
            //将三级插入到二级的children
            if(!empty($levelThree)) {
                foreach ($levelThree as $grandchild) {
                    if (isset($children[$grandchild->area_parent_id])) {
                        $children[$grandchild->area_parent_id]['children'][$grandchild->area_id] = $grandchild->toArray();
                    }
                }
            }
            //将二级插入到一级的children
            if(!empty($children)) {
                foreach ($children as $child) {
                    if (isset($parents[$child['area_parent_id']])) {
                        $parents[$child['area_parent_id']]['children'][$child['area_id']] = $child;
                    }
                }
            }
            Cache::put('area', $parents, 60);
            return $parents;
        }));
    }
}