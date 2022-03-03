<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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


        $products = $query->paginate(30);
        
        return [
            'products' => $products,
            'filters' => $this->getProductFilter($params),
        ];

    } 



    public function getProductFilter($params)
    {

        
        $query = DB::table('pim_flat_catalog');

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
  

        $query->groupBy('group_id');

        $products = $query->get();


       $filters = [
            'sub_category' =>  $this->getFilterLabel('sub_category', $products),
            'price' => $this->getFilterLabel('price', $products),
           'gender' => $this->getFilterLabel('gender', $products),
           'size' => $this->getFilterLabel('size', $products),
           'color_family' => $this->getFilterLabel('color_family', $products),
           'fit' =>  $this->getFilterLabel('fit', $products),
           'bottom_type' =>  $this->getFilterLabel('bottom_type', $products),
           'hood' =>  $this->getFilterLabel('hood', $products),
           'pattern' =>  $this->getFilterLabel('pattern', $products),
           'print_or_pattern_type' =>  $this->getFilterLabel('print_or_pattern_type', $products),
           'sleeve_length' =>  $this->getFilterLabel('sleeve_length', $products),
           'top_length' =>  $this->getFilterLabel('top_length', $products),
           'closure' =>  $this->getFilterLabel('closure', $products),
           'discount' =>  $this->getFilterLabel('discount', $products),

         
       ];



  


       return  $filters;

    }


    public function getFilterLabel ($labels, $products)
    {   

      if ($labels == 'price')   {

        // dd($products->where('price','<', 1000)->count());

      return [
                'Rs0 to Rs1000'    => ['0,1000' , 'product_count' => $products->where('price','<', 1000)->count()  ],
                'Rs1001 to Rs1500' => ['1001,1500' , 'product_count' =>$products->where('price','<', 1500)->where('price','>=', 1001)->count()  ],
                'Rs1501 to Rs2000' => ['1501,2000'  , 'product_count' =>$products->where('price','<', 2000)->where('price','>=', 1501)->count()  ],
                'Rs2001 to Rs3000' => [ '2001,3000' , 'product_count' =>$products->where('price','<', 3000)->where('price','>=', 2000)->count()  ],
                'Rs3001 to Above'  => [ '3001,1000000', 'product_count' =>$products->where('price','<', 100000)->where('price','>=', 3000)->count()  ]
            ] ;

      } elseif ($labels == 'sub_category') {
          $requestData = request()->all();

          if ( count($requestData)) {

                if ($products->count()) {

                    $gender = $products[0]->gender ;
                    $categoryProducts = $this->where('gender', $gender)->groupBy('group_id')->get();
                } else {
                    $gender = 'women' ;
                    $categoryProducts = $this->where('gender', $gender)->groupBy('group_id')->get();
                }
    
                return    array_count_values( Arr::where($categoryProducts->pluck($labels)->toArray(), function ($value, $key) {
                    return ! is_null($value);
                }));
          }else {

            
              
            return    array_count_values( Arr::where($products->pluck($labels)->toArray(), function ($value, $key) {
                return ! is_null($value);
            }));
          }
      } elseif($labels == 'discount') {

            
        return  [
             '16% and Above' => ['16',  $products->where('discount', '>', 16)->count()],
             '20% and Above' => ['20',  $products->where('discount', '>', 20)->count()],
             '30% and Above' => ['30',  $products->where('discount', '>', 30)->count()],
             '33% and Above' => ['33',  $products->where('discount', '>', 33)->count() ],
             '35% and Above' => ['35',  $products->where('discount', '>', 35)->count()],
         ];
       }  else {


          return    array_count_values( Arr::where($products->pluck($labels)->toArray(), function ($value, $key) {
                    return ! is_null($value);
                }));
      }
     

    }


}
