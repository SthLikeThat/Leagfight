<link href="../css/bootstrap-select.css" rel="stylesheet">
<script src="../js/bootstrap-select.js"></script>
 <div class="tabs" style="padding: 5px;">
  <ul class="nav nav-tabs nav-shop">
      <li class="active">
          <a href="#tab-1" data-toggle="tab" aria-expanded="true" data-thing="1" data-sort="#sort-weapon">
              <img src="../images/menu_items/weapon.png" height="25">
            </a>
        </li>
      <li class="">
          <a href="#tab-2" data-toggle="tab" aria-expanded="false" data-thing="3" data-sort="#sort-armor">
              <img src="../images/menu_items/helmet.png" height="25" >
            </a>
       </li>
       <li class="">
          <a href="#tab-3" data-toggle="tab" aria-expanded="false" data-thing="2" data-sort="#sort-armor">
              <img src="../images/menu_items/armor.png" height="25" >
            </a>
       </li>
       <li class="">
          <a href="#tab-4" data-toggle="tab" aria-expanded="false" data-thing="5" data-sort="#sort-armor">
              <img src="../images/menu_items/bracers.png" height="25" >
            </a>
       </li>
       <li class="">
          <a href="#tab-5" data-toggle="tab" aria-expanded="false" data-thing="4" data-sort="#sort-armor">
              <img src="../images/menu_items/leggings.png" height="25" >
            </a>
       </li>
        <li class="">
          <a href="#tab-6" data-toggle="tab" aria-expanded="false" data-thing="6" data-sort="#sort-armor">
              <img src="../images/menu_items/shield.png" height="25" >
            </a>
       </li>
        <li class="">
          <a href="#tab-7" data-toggle="tab" aria-expanded="false" data-thing="7" data-sort="#sort-something">
              <img src="../images/menu_items/smth.png" height="25" >
            </a>
       </li>
       <div class="right-sort sort-shop" id="sort-weapon" >
          Сортировка:
           <select class="selectpicker show-tick" data-width="auto" data-sort="type">
              <option value="0">Любой тип</option>
              <option value="1">Одноручное</option>
              <option value="2">Двуручное</option>
              <option value="3">Древковое</option>
          </select>
          <select class="selectpicker show-tick" data-width="auto" data-sort="typedamage">
              <option value="0">Любой урон</option>
              <option value="1">Колющее</option>
              <option value="2">Режущее</option>
              <option value="3">Дробящее</option>
          </select>
      </div>
      <div class="right-sort sort-shop" id="sort-armor" style="display: none;">
          Сортировка:
           <select class="selectpicker show-tick" data-width="auto" data-sort="type">
              <option value="0">Любой тип</option>
              <option value="1">Лёгкая</option>
              <option value="2">Средняя</option>
              <option value="3">Тяжелая</option>
          </select>
      </div>
      
       <div class="right-sort sort-shop" id="sort-something" style="display: none;"></div>
  </ul>
  <div class="tab-content">
      <div class="tab-pane fade active in" id="tab-1">
          <p>%weapon%</p>
      </div>
      <div class="tab-pane fade" id="tab-2"></div>
      <div class="tab-pane fade" id="tab-3"></div> 
      <div class="tab-pane fade" id="tab-4"></div> 
      <div class="tab-pane fade" id="tab-5"></div>
      <div class="tab-pane fade" id="tab-6"></div>
      <div class="tab-pane fade" id="tab-7"></div>
  </div>
</div>