<template>
   <div >
      <div class="row pb-3 mb-5 border-bottom">
         <div class="col-12">
            <h3>Filters</h3>
         </div>
      </div>
      <div class="">
         <div class="accordion" id="accordionExample">
            <div class="accordion-item" v-for="(filter, index) in filters" :key="index">
                
               <h2 class="accordion-header" :id="index" v-if="Object.keys(filter).length">
                  <button class="accordion-button filter-title" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse'+index" aria-expanded="true" :aria-controls="'collapse'+index">
                  {{index | formatFilterLabel}}
                  </button>
               </h2>
               <div :id="'collapse'+ index"  class="accordion-collapse collapse show" v-if="Object.keys(filter).length" :aria-labelledby="index" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                     <ul>
                        <li v-for="(optionCount, optionIndex) in filter" :key="optionIndex">
                           <div class="form-check" v-if="index == 'price'">
                              <input class="form-check-input" type="checkbox" @click="applyFilter" :name="index" :value="optionCount[0]" :id="optionIndex"  :checked="filterKey.includes(optionCount[0].split(',')[0])">
                              <label class="form-check-label" :for="optionIndex" >
                              {{optionCount[0]   | formatPrice}} ({{optionCount.product_count}}) 
                              </label>
                           </div>
                           <div class="form-check" v-else-if="index == 'discount'">
                              <input class="form-check-input" type="checkbox" @click="applyFilter" :name="index" :value="optionCount[0]" :id="optionIndex"  :checked="filterKey.includes(optionCount[0])">
                              <label class="form-check-label" :for="optionIndex">
                              {{optionCount[0]  | formatDiscount}} ({{optionCount[1]}}) 
                              </label>
                           </div>
                           <div class="form-check" v-else>
                              <input class="form-check-input" type="checkbox" @click="applyFilter" :name="index" :value="optionIndex" :id="optionIndex" :checked="filterKey.includes(optionIndex)">
                              <label class="form-check-label" :for="optionIndex">
                              {{optionIndex}} ({{optionCount}})
                              </label>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script>

    export default {
        data () {
            return  {
                appliedFilters: [],
                filterKey: []
            }
        },

        props:['filters', 'getFilteredProducts'],

        mounted () {
            
            const params = new URLSearchParams(window.location.search)

            params.forEach((param,index) => {

                param.split(',').forEach((pr) => {

                    this.filterKey.push(pr)    
                })
                
                
            this.appliedFilters.push(index+'='+param)
                
            })
        },

        filters: {
            formatPrice : function (price) {

                const range = price.split(',')

                if (range[1] == 1000000) {
                    range[1] = 'Above'
                }
                
                return `Rs ${range[0]} to Rs ${range[1]}`
            },

            formatFilterLabel: function (label) {
                const newLabel = label.replaceAll('_', ' ')
                return newLabel;
            },

            formatDiscount : function (discount ) {

                return discount +'% and Above'
            }
        },        

        methods: {
            applyFilter : function (e) {
                const newf = [e.target.name, e.target.value].join('=');
                let url  =  window.location.origin +"?"+ this.appliedFilters.join('&')
                if (e.target.checked) {

                    const availableFilters  = this.appliedFilters.filter(item => item.startsWith(e.target.name))

                    if (availableFilters.length) {
                        const index = this.appliedFilters.indexOf(availableFilters[0]);
                        const newaf = availableFilters[0]+','+e.target.value
                        this.appliedFilters.splice(index, 1 , newaf)
                    } else {

                        this.appliedFilters.push(newf)
                    }

                    url  =  window.location.origin +"?"+ this.appliedFilters.join('&')
                } else {
                    let urlpar = new URL(window.location.href);
                    let params = new URLSearchParams(urlpar.search);
                    // let uncheckParamValues =  params.getAll(e.target.name)
                    // let remainingValues  = uncheckParamValues.filter(rvalues => console.log(rvalues))
                    // console.log(remainingValues, uncheckParamValues);
                    // if (remainingValues.length == 0) {
                    //     params.delete(e.target.name )
                    // } else {
                    //     remainingValues.forEach((element,index) => {
                    //         if (index == 0) {
                    //              params.set(e.target.name , element)
                    //         } else {
                    //             params.append(e.target.name , element)
                    //         }
                    //     });

                    // }

                    params.delete(e.target.name )

                    this.appliedFilters = [];

                    params.forEach((param,index) => {
                        
                           const newf = [index, param].join('=');
                           this.appliedFilters.push(newf)
                    })

                    url  =  window.location.origin +"?"+ params.toString()

                    
                }

                window.history.pushState({}, '', url);

                this.getFilteredProducts()

            } 
        }
    }
</script>

<style>

    .filter-title {
        font-size: 16px;
        color: #000;
        text-decoration: none;
        display: block;
        line-height: 22.5px;
        text-transform: capitalize;
    }

    .accordion-item, .accordion-button {
        background-color: #fff !important
    }

    #accordionExample {
        height: 200vh;
        overflow-y: auto;
    }

    ul {
        list-style: none;
    }
    .form-check-label {
        cursor: pointer;
    }
    .sidebar {
        border-right: 1px solid #f3f3f3;
    }

    
</style>