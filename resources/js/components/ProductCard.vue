<template>
      <div class="product-card">
        <div class="card" >
            <img  :src="baseImageUrl" class="card-img-top" alt="">
            <a href="" class="whishlist">
                <i class="fa fa-heart-o"></i>
            </a>
            <a href="" class="similar">
                 <i><img src="https://www.getketch.com/images/similar.png" alt=""></i>
            </a>
            <a href="" class="view-details">
                <span class="view-details-btn">
                    View Detail
                </span>
            </a>
            <div class="card-body">
                <h5 class="card-title">{{product.name}}</h5>
                
                <p class="card-text">{{'Product ID: ' + product.id_product}}</p>
                <p class="card-text">{{'RS: ' + product.selling_price}} <strike class="pl-3 pr-3"><span class="text-muted pr-3"> {{	product.price }}</span>  </strike><span class="text-danger pl-3">{{product.discount + ' % OFF' }} </span> </p>
                <p class="card-text">{{'Size: ' + product.sizes}}</p>
                
            </div>
        </div>
    </div>
</template>

<script>


export default ({

    data() {
        return ({
            baseImageUrl: 'https://getketchadmin.getketch.com/product/'
        })
    },
  
  props: ['product'],

  mounted() {
      document.querySelectorAll('.product-card').forEach(function(card) {

        card.addEventListener('mouseover', function () {

            card.classList.add('shadow', 'bg-body' ,'rounded')
        })

        card.addEventListener('mouseout', function () {
            card.classList.remove('shadow', 'bg-body' ,'rounded')
        })
    })

    this.baseImageUrl = this.baseImageUrl+this.product.sku+'/300/'+ this.product.image;

  }
})


</script>

<style scoped>
    .product-card {
        cursor: pointer;
    }

    .view-details {
            position: absolute;
            font-size: 13px;
            line-height: 38px;
            width: 100%;
            text-align: center;
            top: 266px;
            text-decoration: none;
            opacity: 1;
            z-index: 2;
            background: #fff;
            padding: 5px 15px 0;
            /* border: 1px solid gray; */
            display: none;
    }

    .view-details-btn {
        color: #282c3f;
        border: 1px solid #d4d5d9;
        font-size: 12px;
        cursor: pointer;
        line-height: 30px;
        border-radius: 3px;
        display: block;
        text-transform: uppercase;
    }
    .whishlist {
        left: 10px;
    }

    .similar  {
        right: 10px;
    }

    .whishlist, .similar {
        z-index: 2;
        position: absolute;
        display: none;
        bottom: 258px;
        /* font-size: 0; */
        height: 30px;
        width: 30px;
        text-align: center;
        background-color: #f1f2f1;
        border: 1px solid #d4d5d9;
        border-radius: 30px;
        opacity: 1;
        color: #626262;
        text-transform: uppercase;
        text-decoration: none;
        pointer-events: none;
        padding-top: 5px;
        transition: all .1s;
        
    }

    .similar img {
        width: 17px;
        display: inline-block;
        vertical-align: top;
        top: 2px;
        position: relative;
    }

    .product-card:hover .similar,.product-card:hover .whishlist,.product-card:hover .view-details{
        display: block;
    }

.card-img-top {
    height: 302.58px;
    object-fit: cover;
}

</style>
