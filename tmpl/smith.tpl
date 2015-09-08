<div class="smith">
    <div class="col-lg-3 navbar navbar-default navbar-width">
        <div class="title-smith">Характеристики</div>
        <div class="smith-info"></div>
    </table>
    </div>

    <div class="col-lg-3 navbar navbar-default navbar-width smith-menu" style="text-align: center">
        <div class="title-smith">Вещь на улучшение</div>
        <div class="inventory-item item_to_upgrade" data-on="0" data-show="0" data-hash="b00rI9Yd" data-info="">
           <div class="inventory-item-image">
                <img src="../images/cloth/mini/0.png" height="60">
            </div>             
        </div>
        <div class="smith-up" data-type="1">
          <div class="up-char-row">
              <div class="train-image">
                  <img src="../images/icons/damage.png" height="30" data-title="Урон">
                  <span class="value-smith" id="value-smith-damage"></span>
              </div>
               <button class="btn btn-success" data-make="del" data-target="damage">
                    <img src="../images/del1.png" height="20">
                </button>
                <input type="text" class="form-control training-input" placeholder="0" id="input-damage" disabled>
                <button class="btn btn-success" data-make="up" data-target="damage">
                    <img src="../images/pump1.png" height="20">
                </button>
                <div class="train-summ" id="train-summ-damage">0</div>
            </div>
            <div class="up-char-row">
              <div class="train-image">
                  <img src="../images/icons/crit.png" height="30" data-title="Крит">
                  <span class="value-smith" id="value-smith-crit"></span>
              </div>
               <button class="btn btn-success" data-make="del" data-target="crit">
                    <img src="../images/del1.png" height="20">
                </button>
                <input type="text" class="form-control training-input" placeholder="0" id="input-crit" disabled>
                <button class="btn btn-success" data-make="up" data-target="crit">
                    <img src="../images/pump1.png" height="20">
                </button>
                <div class="train-summ" id="train-summ-crit">0</div>
            </div>
        </div>
         <div class="smith-up" data-type="2">
          <div class="up-char-row">
              <div class="train-image">
                  <img src="../images/icons/armor.png" height="30" data-title="Броня">
                  <span class="value-smith" id="value-smith-armor"></span>
              </div>
               <button class="btn btn-success" data-make="del" data-target="armor">
                    <img src="../images/del1.png" height="20">
                </button>
                <input type="text" class="form-control training-input" placeholder="0" id="input-armor" disabled>
                <button class="btn btn-success" data-make="up" data-target="armor">
                    <img src="../images/pump1.png" height="20">
                </button>
              <div class="train-summ" id="train-summ-armor">0</div>
            </div>
        </div>
        <div class="result-sum-row-smith">
             <div class="result-sum">
             Скидка: <span id="discount">0</span>%
             </div>
             <div class="result-sum">
             Итого: <span id="total-sum-discount">0</span><img src="../images/diamond.png" height="20">
             </div>
         </div>
    </div>

    <div class="navbar navbar-default navbar-width col-lg-6 inventory-smith">
        <div class="title-smith">Инвентарь</div>
        %inventory%
    </div>
</div>