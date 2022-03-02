<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class PimFlatCatalog extends Model
{
    use HasFactory;

    protected $table = "pim_flat_catalog";



    public function scopeGetAll($query) {

        $params = Arr::except(request()->all(), ['page']);

        foreach ($params as $key=> $param) {

            if ($key == 'price') {

                $query->WhereBetween($key, explode(",",$param));

            } 
            elseif($key == 'search-term') { 
              $columns  =    collect(PimFlatCatalog::first())->keys();
                foreach ($columns as  $column) {
                    foreach (explode(" ", $param) as  $key=> $ppram) {
                        if($key == 0 ) {
                            $query->orWhere($column,'like', '%'.$ppram.'%');
                        } else {
                            $query->where($column,'like', '%'.$ppram.'%');
                            
                        }
                    }
                }

            }
            elseif($key == 'discount') {
                $query->where($key, '>', explode(",",$param));
            }

            elseif($key == 'sortBy') {

                switch($param) {
                    case  'low_price' : {
                        $query->orderBy('price', 'asc') ;
                    }
                    case  'relavance' : {

                    }
                    case  'heigh_price' : {
                        $query->orderBy('price', 'desc') ;
                    }
                    case  'discount' : {
                        $query->orderBy('discount', 'desc') ;
                    }
                }
                
            }
            else {

                $query->whereIn($key, explode(",",$param));
            }
        }

        // dd($query->toSql());

        $query->distinct('id_product')->groupBy('group_id');


        return $query->paginate(30);

    }



    public function scopeGetProductFilter($query)
    {
       $productsArr = $query->take(10000)->get();

       $filters = [
           'sub_category' => $this->getFilterLabel( array_unique(Arr::pluck($productsArr, 'sub_category')), 'sub_category' ) ,
        //    'price' => $this->getFilterLabel( [
        //        'Rs0 to Rs1000' => '0,1000',
        //        'Rs1001 to Rs1500' => '1001,1500',
        //        'Rs1501 to Rs2000' => '1501,2000',
        //        'Rs2001 to Rs3000' => '2001,3000',
        //        'Rs3001 to Above' => '3001,1000000'
        //    ] , 'price'),
           'gender' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'gender')), [null]), 'gender'),
           'size' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'size')), [null]), 'size'),
           'color_family' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'color_family')), [null]), 'color_family'),
           'fit' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'fit')), [null]), 'fit'),
           'bottom_type' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'bottom_type')), [null]), 'bottom_type'),
           'hood' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'hood')), [null]), 'hood'),
           'pattern' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'pattern')), [null]), 'pattern'),
           'print_or_pattern_type' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'print_or_pattern_type')), [null]), 'print_or_pattern_type'),
           'sleeve_length' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'sleeve_length')), [null]), 'sleeve_length'),
           'top_length' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'top_length')), [null]), 'top_length'),
           'closure' =>   $this->getFilterLabel(  Arr::except(   array_unique(Arr::pluck($productsArr, 'closure')), [null]), 'closure'),
        //    'discount' =>   [
        //        '16% and Above' => '16',
        //        '20% and Above' => '20',
        //        '30% and Above' => '30',
        //        '33% and Above' => '33',
        //        '35% and Above' => '35',
        //    ],

         
       ];


       return $filters;

    }


    public function getFilterLabel ($labels, $filter)
    {   
        $newLabel = [];
        foreach ($labels as $key =>$label) {
            $newLabel[$key]['label'] = $label;
            if ($filter == 'price') {
                $newLabel[$key]['count'] = $this->select('id_product', 'group_id')->whereIn($filter, $label)->distinct('id_product')->groupBy('group_id')->count();
            } else {
                // dd($this->where($filter, $label)->distinct('id_product')->groupBy('group_id')->get());
                $newLabel[$key]['count'] = $this->select('id_product', 'group_id')->where($filter, $label)->distinct('id_product')->groupBy('group_id')->get()->count();   
            }
        }




        return $newLabel;
    }


    public  function sizes()
    {
       
        return $this->where('parent_id', $this->id_product)->pluck('size');
    }


}
