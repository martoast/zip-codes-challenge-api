<?php

namespace App\Http\Resources;

use App\Http\Resources\FederalEntityResource;
use App\Http\Resources\MunicipalityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Foundation\Application;

class ZipCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'federal_entity' => new FederalEntityResource($this->whenLoaded('federalEntity')),
            'settlements' => SettlementResource::collection($this->whenLoaded('settlements')),
            'municipality' => new MunicipalityResource($this->whenLoaded('municipality'))
        ]);
    }
}
