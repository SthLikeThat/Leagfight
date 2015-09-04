<div class="panel panel-default shop-item" data-typedamage="%type_damage_number%" data-type="%type_number%">
  <div class="panel-heading">
      <h4 class="panel-title">
          <div>%title% </div>
      </h4>
  </div>
  <div class="panel-body">
    <div class="shop-item-image">
        <img src="../images/cloth/%name%.png"/>
    </div>	
    <div class="shop-item-right-info">
        <div class="shop-item-right-info-block">
            <img src="../images/icons/lvl.png"> %required_lvl%
        </div>
        <div class="shop-item-right-info-block"> 
            <img src="../images/icons/hp.png"> %value_effect%%
        </div>
    </div>	
    <div class="shop-item-bottom-info">
     %description%
      </div>
  </div>
  <div class="panel-footer">
      <div class="price-shop">
          %price% <img src="../images/coinBlack.png" />
      </div>
      <button class="btn btn-success btn-buyThing" onclick="buyThing(%id%)">Купить</button>
  </div>
</div>