<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
          "id"=>$this->id,  
          "name"=>$this->name,  
          "slug"=>$this->slug,  
          "logo"=>"storage/".$this->logo,  
        ];
    }
}
