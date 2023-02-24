<?php

namespace Modules\Ilocations\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  use Translatable;

  protected $table = 'ilocations__countries';
  public $translatedAttributes = [
    'name',
    'full_name'
  ];
  protected $fillable = [
    'currency',
    'currency_symbol',
    'currency_code',
    'currency_sub_unit',
    'region_code',
    'sub_region_code',
    'country_code',
    'iso_2',
    'iso_3',
    'calling_code',
    'status'
  ];

  public function provinces()
  {
    return $this->hasMany(Province::class);
  }

  public function children()
  {
    return $this->hasMany(Province::class)->with("children");
  }

  public function cities()
  {
    return $this->hasMany(City::class);
  }
  
  public function getNameAttribute(){
    
    $currentTranslations = $this->getTranslation(locale());
    
    if (empty($currentTranslations) || empty($currentTranslations->toArray()["name"])) {
      
      $model = $this->getTranslation(\LaravelLocalization::getDefaultLocale());
  
      if(empty($model)) return "";
      return $model->toArray()["name"] ?? "";
    }
    
    return $currentTranslations->toArray()["name"];
    
  }

  public function geozones()
  {
    return $this->morphToMany(Geozones::class, 'geozonable', 'ilocations__geozonables', 'geozone_id');
  }

    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['asgard.ilocations.config.relations.country', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);
            $bound = $function->bindTo($this);

            return $bound();
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }
}
