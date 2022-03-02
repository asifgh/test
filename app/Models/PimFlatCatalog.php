<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class PimFlatCatalog extends Model
{
    use HasFactory;

    protected $table = "pim_flat_catalog";

    protected $primaryKey = 'id_product';

    public function scopeGetAll($query) {

        $params = Arr::except(request()->all(), ['page']);

        foreach ($params as $key=> $param) {

            if ($key == 'price') {

                $query->whereBetween('selling_price', explode(",",$param));

            } 
            elseif($key == 'search-term') { 
              $columns  =    collect($this->first())->keys();
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
                $query->where($key, '>', $param);
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


        $query->select('*');

        $query->selectRaw('group_concat(size) as sizes');

        $query->groupBy('group_id');
        
        
        return [
            'products' =>  $query->paginate(30),
            'filters' => $this->getProductFilter($query),
        ];

    } 



    public function getProductFilter($query)
    {

     $filterData = $this->select('sub_category', 'gender', 'size', 'price', 'discount',
        'color_family',
        'fit',
        'bottom_type',
        'hood',
        'pattern',
        'print_or_pattern_type',
        'sleeve_length',
        'top_length',
        'closure')->groupBy('group_id')->get();


       $filters = [
            'sub_category' =>  $this->getFilterLabel('sub_category', $filterData),
            'price' =>  [
                'Rs0 to Rs1000'    => ['0,1000' , 'product_count' => $this->select('selling_price')->whereBetween('selling_price',[0, 1000] )->groupBy('group_id')->get()->count()] ,
                'Rs1001 to Rs1500' => ['1001,1500' , 'product_count' =>$this->select('selling_price')->whereBetween('selling_price',[1001, 1500] )->groupBy('group_id')->get()->count()],
                'Rs1501 to Rs2000' => ['1501,2000'  , 'product_count' => $this->select('selling_price')->whereBetween('selling_price',[1501, 2000] )->groupBy('group_id')->get()->count()],
                'Rs2001 to Rs3000' => [ '2001,3000' , 'product_count' =>$this->select('selling_price')->whereBetween('selling_price',[2001, 3000] )->groupBy('group_id')->get()->count()],
                'Rs3001 to Above'  => [ '3001,1000000', 'product_count' =>$this->select('selling_price')->whereBetween('selling_price',[3001, 1000000] )->groupBy('group_id')->get()->count()]
            ] ,
           'gender' => $this->getFilterLabel('gender', $filterData),
           'size' => $this->getFilterLabel('size', $filterData),
           'color_family' => $this->getFilterLabel('color_family', $filterData),
           'fit' =>  $this->getFilterLabel('fit', $filterData),
           'bottom_type' =>  $this->getFilterLabel('bottom_type', $filterData),
           'hood' =>  $this->getFilterLabel('hood', $filterData),
           'pattern' =>  $this->getFilterLabel('pattern', $filterData),
           'print_or_pattern_type' =>  $this->getFilterLabel('print_or_pattern_type', $filterData),
           'sleeve_length' =>  $this->getFilterLabel('sleeve_length', $filterData),
           'top_length' =>  $this->getFilterLabel('top_length', $filterData),
           'closure' =>  $this->getFilterLabel('closure', $filterData),
           'discount' =>   [
               '16% and Above' => ['16',  $this->select('discount')->where('discount', '>', 16)->groupBy('group_id')->get()->count()],
               '20% and Above' => ['20',  $this->select('discount')->where('discount', '>', 20)->groupBy('group_id')->get()->count()],
               '30% and Above' => ['30',  $this->select('discount')->where('discount', '>', 30)->groupBy('group_id')->get()->count()],
               '33% and Above' => ['33',  $this->select('discount')->where('discount', '>', 33)->groupBy('group_id')->get()->count() ],
               '35% and Above' => ['35',  $this->select('discount')->where('discount', '>', 35)->groupBy('group_id')->get()->count()],
           ],

         
       ];



  


       return  $filters;

    }


    public function getFilterLabel ($labels, $filterData)
    {   

      if ($labels == 'price')   {

      return  [
                'Rs0 to Rs1000'    =>   array_count_values( Arr::where($filterData->pluck($labels)->toArray(), function ($value, $key) {
                    return $value < 1000;
                })),
                // 'Rs1001 to Rs1500' => ['1001,1500' , 'product_count' =>$this->select('selling_price')->whereBetween('selling_price',[1001, 1500] )->groupBy('group_id')->get()->count()],
                // 'Rs1501 to Rs2000' => ['1501,2000'  , 'product_count' => $this->select('selling_price')->whereBetween('selling_price',[1501, 2000] )->groupBy('group_id')->get()->count()],
                // 'Rs2001 to Rs3000' => [ '2001,3000' , 'product_count' =>$this->select('selling_price')->whereBetween('selling_price',[2001, 3000] )->groupBy('group_id')->get()->count()],
                // 'Rs3001 to Above'  => [ '3001,1000000', 'product_count' =>$this->select('selling_price')->whereBetween('selling_price',[3001, 1000000] )->groupBy('group_id')->get()->count()]
            ];

      } else {

        // dd(   $filterData->pluck($labels)->toArray());

          return    array_count_values( Arr::where($filterData->pluck($labels)->toArray(), function ($value, $key) {
                    return ! is_null($value);
                }));
      }
     

    }


}
