<?php
namespace Modules\Product\Repositories;

use Modules\Setup\Entities\Tag;

class TagRepository
{

    public function tagList(){
        return Tag::latest()->paginate(10);
    }

    public function getByTag($tag){
        return Tag::where('name', $tag)->first();
    }

}
