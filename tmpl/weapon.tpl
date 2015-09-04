<div class="panel panel-default shop-item" data-typedamage="%type_damage%" data-type="%type%">
  <div class="panel-heading">
      <h4 class="panel-title">
          <div>%name% <h5 style="float: right; margin-top: 0;"> [%type_word%]</h5></div>
      </h4>
  </div>
  <div class="panel-body">
    <div class="shop-item-image">
        <img src="../images/cloth/%id%.png"/>
    </div>	
    <div class="shop-item-right-info">
        <div class="shop-item-right-info-block"> %type_damage_word%</div>
        <div class="shop-item-right-info-block">
            <img src="../images/icons/lvl.png"> %required_lvl%
        </div>
        <div class="shop-item-right-info-block"> 
            <img src="../images/icons/damage.png"> %damage%
        </div>
        <div class="shop-item-right-info-block">
            <img src="../images/icons/crit.png"> %crit%
        </div>
    </div>	
     <div class="shop-item-bottom-info">
        <div class="bottom-char-shop"><img src="../images/icons/strengh.png" > %bonus_strengh%</div>	
        <div class="bottom-char-shop"><img src="../images/icons/defence.png" > %bonus_defence%</div>	
        <div class="bottom-char-shop"><img src="../images/icons/agility.png" > %bonus_agility%</div>
        <div class="bottom-char-shop"><img src="../images/icons/physique.png" > %bonus_physique%</div>	
        <div class="bottom-char-shop"><img src="../images/icons/mastery.png" > %bonus_mastery%</div>
      </div>
  </div>
  <div class="panel-footer">
      <div class="price-shop">
          %price% <img src="../images/coinBlack.png" />
      </div>
      <button class="btn btn-success btn-buyThing" onclick="buyThing(%id%)">Купить</button>
  </div>
</div>