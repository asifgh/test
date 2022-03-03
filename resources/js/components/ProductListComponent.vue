<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="sidebar col-lg-3 col-sm-12">
                <sidebar :filters="filters" :getFilteredProducts="getFilteredProducts" />
            </div>
            <div class="content col-lg-9 col-sm-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="toolbar row pb-3 mb-5 border-bottom">
                            <div class="col-8">
                                <p class="total"> Showing {{total}} Results </p>
                            </div>

                            <div class="col-4">

                            <select name="sortby" id="" v-model="sortBy" @change="productSort" class="form-control sortby float-end">
                                <option value="relavance" selected >Relavance</option>
                                <option value="low_price">Low to Heigh Price</option>
                                <option value="heigh_price">Heigh to Low Price</option>
                                <option value="discount">Discount</option>
                            </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" v-if=" !loading">
                    <div class="col-lg-3 col-sm-12 pb-3" v-for="(product,index) in products" :key="index">
                        <ProductCard :product="product" />
                    </div>
                </div>
                <div class="row" v-else>
                        <LoadingComponent :active.sync="loading" 
                        :can-cancel="false" 
                        :is-full-page="true"></LoadingComponent>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

const axios = require('axios');

import ProductCard from './ProductCard.vue'
import LoadingComponent from 'vue-loading-overlay';
// Import stylesheet
import 'vue-loading-overlay/dist/vue-loading.css';


export  default {

    data () {
        return  {
            products: [],
            filters: [],
            total: 0,
            loading:false,
            page:0,
            url:'http://localhost/getketch/public/api/products/st-search?',
            sortBy:''
        }
    },

    components: {
        ProductCard,
        LoadingComponent
    },
    mounted () {        
        this.getProducts()
        this.getNextPageProduct()

       this.$root.$on('eventing', term => {
            this.searchProduct(term)
        });
    },

   methods : {
       getProducts :  function ()  {

            const params = new URLSearchParams(window.location.search)

            this.loading = true

            if (params.toString()) {
                let tempurl = this.url.split('?')[0]
                this.url = tempurl+'?'+params.toString()
            }

           axios.get(this.url) 
             .then((response) =>  {

                // handle success
                this.products = response.data.products.data
                this.filters = response.data.filters
                this.total = response.data.products.total
                this.loading = false
            })

            .catch(function (error) {
                // handle error
                console.log(error);
            })
       },

        getNextPageProduct() {

            // console.log((this.products.length <= this.total), this.total, this.products.length)

            
        
                this.loading = true
                
                window.onscroll = () => {
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
                    console.log(this.products.length, this.total)
                    if (bottomOfWindow) {
                    if ( (this.products.length <= this.total)) {    
                        this.loading = true
                        this.page++;
                        const query  =  window.location.href.split('?')[1]
                        if (query) {
                            this.url = `http://localhost/getketch/public/api/products/st-search?page=`+this.page+'&'+ query
                        }
                        axios.get(this.url).then(response => {
        
                            this.products = [...this.products, ...response.data.products.data]
                            this.loading = false
        
                        });
                    }
                    }
                }
            

        },

        getFilteredProducts: function() {

            this.loading = true

            const query  =  window.location.href.split('?')[1]

            this.url = `http://localhost/getketch/public/api/products/st-search?` + query 
            
            axios.get(this.url) 
             .then((response) =>  {
                 console.log(response.data.products)
                // handle success
                this.products = [ ...response.data.products.data]
                this.filters = response.data.filters
                this.total = response.data.products.total
                this.loading = false
            })

            .catch(function (error) {
                // handle error
                console.log(error);
            })
        },

        searchProduct: function (term) {

        //  this.url  =  this.url.split('?')[0] +'?' + 'search-term='+term ;
         const href = window.location.origin+'?'+ 'search-term='+term
         window.history.pushState({}, '', href);

         this.getProducts()

        },

        productSort: function (e) {

            

            let url = new URL(window.location.href);
            let params = new URLSearchParams(url.search);
            
            if (params.has('sortBy') ) {
                params.set('sortBy', this.sortBy)
            } else {
                params.append('sortBy', this.sortBy);
            }


            const href = url.origin+'?'+ params.toString()

            window.history.pushState({}, '', href);

            params.forEach(function(para){console.log(para)})

            this.getProducts()

            
        }
         
   }
}
</script>

<style>

.sortby {
    width: 200px;
}
</style>