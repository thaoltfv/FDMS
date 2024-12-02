<template>
  <div class="container-fluid" :style="isMobile() ? {'margin':'0', 'padding': '0'} : {}">
    <div v-if="!isMobile()" class="contain-detail">
      <div class="loading" :class="{'loading__true': isSubmit}">
        <a-spin />
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title text-nowrap">Phiên bản</h3>
              <div class="ml-2" style="width:150px">
                <InputCategoryData
                  v-model="version"
                  vid="version"
                  label="version"
                  placeholder="Chọn phiên bản"
                  :options="optionsVersion"
                  class="label-none"
                  @change="changeVersion"
                />
              </div>
            </div>
            <div v-if="form.id" class=" color_content card-status">
								{{form.id ? `TSSS_${form.id}` : 'TSSS'}}
							</div>
          </div>
        </div>
      </div>
      <div class="card">

        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title text-nowrap">Thông tin giao dịch</h3>
            <img class="img-dropdown" :class="!showInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfo = !showInfo">
          </div>
        </div>
        <div class="card-body card-info" v-if="showInfo">
          <div class="container-fluid color_content">
          <div class="row"  v-if="form.asset_type_id !== 39">
            <div class="col-12 col-lg-8" style="padding-left: 0;">
          <div class="d-grid">
            <div class="content-detail" v-if="this.form.source !== null">
              <p class="content-title">Nguồn thông tin:</p>
              <p class="content-name">{{this.form.source.description}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail" v-if="this.form.asset_type != null">
              <p class="content-title">Loại tài sản:</p>
              <p class="content-name" v-if="this.form.asset_type != null">{{this.form.asset_type.description}}</p>
              <p class="content-name" v-if="this.form.asset_type === null">Chưa có loại tài sản</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Loại giao dịch:</p>
              <p class="content-name" v-if="this.form.transaction_type !== null">{{this.form.transaction_type.description}}</p>
              <p class="content-name" v-if="this.form.transaction_type === null">Chưa có loại giao dịch</p>
            </div>
            <div class="content-detail" v-if="this.form.transaction_type !== null">
              <p class="content-title">Giá (VND):</p>
              <p class="content-name">{{formatCurrency(this.form.total_amount)}}đ</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Thời điểm đăng tin:</p>
              <p class="content-name"> {{formatDate(this.form.public_date)}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Người liên hệ</p>
              <p class="content-name">{{this.form.contact_person}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">SĐT người liên hệ:</p>
              <p class="content-name">{{this.form.contact_phone}}</p>
            </div>
          </div>
          <p class=" title" v-if="form.asset_type_id !== 39">Vị trí tài sản</p>

          <div class="d-grid" v-if="form.asset_type_id !== 39">
                <div class="content-detail">
                  <p class="content-title">Tỉnh/Thành:</p>
                  <p class="content-name">{{this.form.province !== undefined && this.form.province !== null ? this.form.province.name : ''}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Quận/Huyện:</p>
                  <p class="content-name">{{this.form.district !== undefined && this.form.district != null ? this.form.district.name : ''}}</p>
                </div>

                <div class="content-detail">
                  <p class="content-title">Phường/Xã:</p>
                  <p class="content-name">{{this.form.ward !== undefined && this.form.ward !== null ? this.form.ward.name : ''}}</p>
                </div>
          <!-- </div>
          <div class="d-grid" v-if="form.asset_type_id !== 39"> -->
                <div class="content-detail">
                  <p class="content-title">Đường:</p>
                  <p class="content-name">{{this.form.street !== undefined && this.form.street !== null ? this.form.street.name: ''}}</p>
                </div>
                <div class="content-detail" >
                  <p class="content-title">Đoạn:</p>
                  <p class="content-name">{{this.form.distance !== null && this.form.distance !== undefined ? this.form.distance.detail : 'Chưa có đoạn'}}</p>
                </div>
                <div class="content-detail"  v-if="form.asset_type_id !== 39">
							<p class="content-title">Địa hình:</p>
							<p class="content-name">{{form.topographic_data !== null && form.topographic_data !== undefined ? this.form.topographic_data.description : 'Chưa có địa hình'}}</p>
						</div>
                <!-- <div class="content-detail">
                  <p class="content-title">Tọa độ:</p>
                  <p class="content-name">{{this.form.coordinates}}</p>
                </div> -->
              </div>
          </div>
          <div class="col-12 col-lg-4">
              <div class="d-flex flex-column h-100">
                <div class="form-group-container position-relative w-100">
                  <InputText
                    id="coordinate"
                    :disabledInput="true"
                    v-model="this.form.coordinates"
                    vid="coordinates"
                    label="Tọa độ"
                    class="coordinates"
                    rules="required"
                    />
                  <!-- <div class="img-locate">
                    <img src="@/assets/icons/ic_locate.svg" alt="locate" @click="handleOpenModalMap()">
                  </div> -->
                </div>
                <!-- Map -->
                <div class="col-12 w-100 h-100 mt-3 layer-map" style="flex: 1">
              <div class="d-flex all-map w-100 h-100">
                <div class="main-map w-100 h-100">
                  <div id="mapid" class="layer-map w-100 h-100">
                      <l-map
                        ref="map_step1"
                        :zoom="map.zoom"
                        :center="map.center"
                        :maxZoom="20"
                        :options="{zoomControl: false}"
                      >
                      <l-tile-layer :url="url" :options="{ maxNativeZoom: 20, maxZoom: 20}"></l-tile-layer>
                      <l-tile-layer
                        v-for="tileProvider in tileProviders"
                        :key="tileProvider.name"
                        :name="tileProvider.name"
                        :visible="tileProvider.visible"
                        :url="tileProvider.url"
                        :attribution="tileProvider.attribution"
                        :layer-type="tileProvider.type"
                        :options="{ maxNativeZoom: 20, maxZoom: 20 }"
                      />
                        <!-- <l-tile-layer :url="url"></l-tile-layer> -->
                        <l-control-zoom position="bottomright"></l-control-zoom>

                        <l-control position="bottomleft">
                          <button class="btn btn-map" @click="handleView" type="button">
                            <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                            <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                          </button>
                        </l-control>
                        <l-control-layers position="bottomleft"></l-control-layers>
                        <l-marker :lat-lng="markerLatLng">
                          <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                          <l-tooltip>Vị trí tài sản</l-tooltip>
                        </l-marker>
                      </l-map>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!-- <div class="col-4">
            <div class="d-grid" v-if="form.asset_type_id !== 39">
              <div class="content-detail">
                  <p class="content-title">Tọa độ:</p>
                  <p class="content-name">{{this.form.coordinates}}</p>
                </div>
            </div>
          </div> -->

          </div>
          </div>
          <div class="d-grid" v-if="form.asset_type_id !== 39">
                <!-- <div class="content-detail">
                  <p class="content-title">Thửa đất số:</p>
                  <p class="content-name">{{this.form.land_no !== '' && this.form.land_no !== undefined && this.form.land_no !== null ? this.form.land_no : 'Trống'}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Bản đồ số:</p>
                  <p class="content-name">{{this.form.doc_no !== '' && this.form.doc_no !== undefined && this.form.doc_no !== null ? this.form.doc_no : 'Trống'}}</p>
                </div> -->
          </div>
          <div class="d-grid" v-if="form.asset_type_id === 39">
            <div class="content-detail">
              <p class="content-title">Tên chung cư/dự án:</p>
              <p class="content-name">{{this.form.project !== undefined && this.form.project !== null ? this.form.project.name : ''}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Block (khu):</p>
              <p class="content-name">{{this.form.block ? this.form.block.name : 'Trống'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tầng</p>
              <p class="content-name">{{this.form.floor ? this.form.floor.name : ""}}</p>
            </div>
          </div>
          <div class="d-grid" v-if="form.asset_type_id === 39">
            <div class="content-detail">
              <p class="content-title">Mã căn hộ:</p>
              <p class="content-name">{{this.form.apartment_specification ? this.form.apartment_specification.apartment_name : ""}}</p>
            </div>
          </div>
					<div class="">
						<div class="">
							<p class="content-title">Địa chỉ đầy đủ:</p>
							<p class="content-name">{{this.form.full_address !== '' && this.form.full_address !== undefined && this.form.full_address !== null ? this.form.full_address : 'Không xác định'}}</p>
						</div>

					</div>
        </div>
      </div>

      <div class="card" v-if="form.asset_type_id === 39">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Thông tin block</h3>
            <img class="img-dropdown" :class="!showBlock? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showBlock = !showBlock">
          </div>
        </div>
        <div class="card-body card-info" v-if="showBlock">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Năm xây dựng:</p>
              <p class="content-name"> {{this.form.apartment_specification.handover_year}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng số tầng:</p>
              <p class="content-name">{{this.form.apartment_specification.total_floors}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số tầng hầm:</p>
              <p class="content-name">{{this.form.apartment_specification.nb_basement}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Số tầng ở:</p>
              <p class="content-name">{{this.form.apartment_specification.nb_living_floor}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Số căn hộ mỗi tầng:</p>
              <p class="content-name" >{{this.form.apartment_specification.total_apartments}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số lượng thang máy</p>
              <p class="content-name">{{this.form.apartment_specification.nb_elevator}}</p>
            </div>
          </div>
          <p class="title title-highlight">Tiện ích cơ bản</p>
          <div class="d-grid justify-content-between container-utilities">
            <div class="col-12 text-center form-group-container d-flex" v-for="(basic_utility, index) in basic_utilities" :key="index">
              <div class=" col-12 d-flex justify-content-flex-start align-items-center">
                <label class="input-checkbox" style="margin-right: 10px;">
                  <input type="checkbox" :id="basic_utility.acronym" v-model="form.apartment_specification.utilities" :value="basic_utility.acronym" disabled>
                  <span class="check-mark"/>
                </label>
                <label class="color-black font-weight-bold mr-2 mb-2">{{basic_utility !== undefined && basic_utility !== null ? basic_utility.description : ''}}</label>

              </div>
            </div>
            <div class="col-12 col-md-4 text-center"/>
          </div>
        </div>
      </div>

      <div class="card" v-if="form.asset_type_id === 39">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Thông tin chi tiết căn hộ</h3>
            <img class="img-dropdown" :class="!showApartment? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showApartment = !showApartment">
          </div>
        </div>
        <div class="card-body card-info" v-if="showApartment">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Diện tích (m<sup>2</sup>):</p>
              <p class="content-name">{{ formatNumber(this.form.room_details[0].area)}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số phòng ngủ:</p>
              <p class="content-name">{{this.form.room_details[0].bedroom_num}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Số phòng WC:</p>
              <p class="content-name" >{{this.form.room_details[0].wc_num}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Loại căn hộ:</p>
              <p class="content-name">{{this.form.room_details[0].loai_can_ho !== undefined && this.form.room_details[0].loai_can_ho !== null ? this.form.room_details[0].loai_can_ho.description : ''}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Hướng chính:</p>
              <p class="content-name">{{this.form.room_details[0].direction !== undefined && this.form.room_details[0].direction !== null ? this.form.room_details[0].direction.description : ''}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng quan chất lượng nội thất:</p>
              <p class="content-name">{{this.form.room_details[0].furniture_quality !== undefined && this.form.room_details[0].furniture_quality !== null ? this.form.room_details[0].furniture_quality.description : ''}}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card" v-if="this.form.asset_type_id !== 39">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title">Thông tin thửa đất</h3>
              <b-dropdown class="dropdown-container ml-3" no-caret>
                <template #button-content>
                  <img src="../../assets/icons/ic_more.svg" alt="">
                </template>
                <b-dropdown-item v-for ="(property) in form.properties" :key="property.id" @click="openModalDetail(property)">
                  <div class="dropdown-item-container"><img
                    src="../../assets/icons/ic_paper.svg" alt="">Tài sản đất số {{property.id}}
                  </div>
                </b-dropdown-item>
              </b-dropdown>
            </div>
              <img class="img-dropdown" :class="!showLand? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showLand = !showLand">

          </div>
        </div>
        <div class="card-body card-info card-land" v-if="showLand">
          <div class="contain-table">
            <table class="table-property">
              <thead>
              <tr>
                <th>Mã số</th>
                <th>Số tờ</th>
                <th>Số thửa</th>
                <th>Chiều rộng</th>
                <th>Chiều dài</th>
                <th>Diện tích</th>
                <th>Hình ảnh</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for = "(property) in form.properties" :key="property.id">
                <td>
                  <div class="input-code" @click="openModalDetail(property)">
                    TSD_{{property.id}}
                  </div>
                </td>
                <td>
                  <div>
                    {{property.compare_property_doc !== undefined && property.compare_property_doc !== null && property.compare_property_doc.length > 0 ? property.compare_property_doc[0].doc_num : 'Chưa có số tờ'}}
                  </div>
                </td>
                <td>
                  <div>
                    {{property.compare_property_doc[0] !== undefined && property.compare_property_doc[0] !== null && property.compare_property_doc.length > 0 ? property.compare_property_doc[0].plot_num : 'Chưa có số thửa'}}
                  </div>
                </td>
                <td>
                  {{formatNumber(property.front_side_width)}}m
                </td>
                <td>{{formatNumber(property.insight_width)}}m</td>
                <td style="white-space: nowrap" >
                  <div>
                    {{formatNumber(property.asset_general_land_sum_area)}}
                  </div>
                </td>
                <td>
                  <div class="img-contain img-contain__table" v-if="property.pic.length > 0" @click="openModalImage(property.pic[0])">
                    <img :src="property.pic[0].link" alt="pic">
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <div class="card" v-if="form.asset_type !== null && form.asset_type_id === 38 && form.tangible_assets.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Công trình xây dựng</h3>
          <img class="img-dropdown" :class="!showTable? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showTable =! showTable">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showTable">
        <div class="contain-table">
          <table class="table-property">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Loại</th>
              <th>Cấp nhà</th>
              <th>Chất lượng còn lại</th>
              <th>Diện tích sàn (m<sup>2</sup>)</th>
              <th>Giá trị ước tính (VND)</th>
              <th>Hình ảnh</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for = "(tangible_asset) in form.tangible_assets" :key="tangible_asset.id">
              <td>
                <div class="input-code" @click="openTangibleDetail(tangible_asset)">
                  TSN_{{tangible_asset.id}}
                </div>
              </td>
              <td style="white-space: nowrap">
                {{tangible_asset.building_type !== undefined && tangible_asset.building_type !== null? tangible_asset.building_type.description : ''}}
              </td>
              <td style="white-space: nowrap">
                {{tangible_asset.building_category !== undefined && tangible_asset.building_category !== null ? tangible_asset.building_category.description : ''}}
              </td>
              <td>{{ formatNumber(tangible_asset.remaining_quality) }}%</td>
              <td>{{ formatNumber(tangible_asset.total_construction_base) }}m<sup>2</sup></td>
              <td>
                {{ formatCurrency(tangible_asset.estimation_value) }}đ
              </td>
              <td>
                <div class="d-flex justify-content-center">
                  <div class="img-contain img-contain__table" v-if="tangible_asset.pic.length > 0" @click="openModalImage(tangible_asset.pic[0])">
                    <img :src="tangible_asset.pic[0].link" alt="">
                  </div>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card" v-if="form.asset_type !== null && form.asset_type_id !== 39 && form.other_assets.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Tài sản khác</h3>
          <img class="img-dropdown" :class="!showOther? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showOther = !showOther">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showOther">
        <div class="contain-table">
          <table class="table-property table-property__order">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Loại tài sản</th>
              <th>Giá trị (VND)</th>
              <th>Hình ảnh</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for = "(other_asset) in form.other_assets" :key="other_asset.id">
              <td>
                <div class="input-code" style="cursor: default">
                  PL_{{other_asset.id}}
                </div>
              </td>
              <td>
                  {{other_asset.other_asset !== undefined && other_asset.other_asset !== null ? other_asset.other_asset : ''}}
              </td>
              <td>
                {{formatCurrency(other_asset.total_amount)}}đ
              </td>
              <td>
                <div class="img-contain img-contain__table" v-for="images in other_asset.pic" :key="images.id" @click="openModalImage(images)">
                  <img :src="images.link" alt="">
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Hình ảnh</h3>
          </div>
        </div>
        <div class="card-body">
          <div class="container-img row mr-0 ml-0">
            <div class="img-empty text-center" v-if="form.pic.length === 0">
              <img src="../../assets/images/img_emply.svg" alt="empty">
              <p class="empty-content"> Chưa có hình</p>
            </div>
            <div class="img-contain col-4 col-lg-2" v-for="images in form.pic" :key="images.id" @click="openModalImage(images)">
              <img :src="images.link" alt="img">
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Ước tính đơn giá {{this.form.asset_type_id === 39? 'căn hộ' : 'đất'}}</h3>
            <img class="img-dropdown" :class="!showDeal? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showDeal = !showDeal">
          </div>
        </div>
        <div class="card-body card-info" v-if="showDeal">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Tổng giá trị (VND):</p>
              <p class="content-name">{{ formatCurrency(this.form.total_amount)}}đ</p>
            </div>
            <div class="content-detail" v-if="this.form.asset_type_id === 39">
              <p class="content-title">Diện tích (m<sup>2</sup>):</p>
              <p class="content-name">{{ formatNumber(this.form.room_details[0].area) }}m<sup>2</sup></p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Tổng giá trị {{this.form.asset_type_id === 39? 'căn hộ' : 'tài sản'}} sau điều chỉnh (VND):</p>
              <p class="content-name">{{ formatCurrency(this.form.total_estimate_amount)}}đ </p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tỉ lệ điều chỉnh (%): </p>
              <p class="content-name">{{ formatNumber(this.form.adjust_percent)}}% </p>
            </div>
            <div class="content-detail">
              <p class="content-title">Giá trị giảm/tăng (VND): </p>
              <p class="content-name">{{ formatCurrency(this.form.adjust_amount)}}đ </p>
            </div>
          </div>
          <div class="d-grid" v-if="this.form.asset_type_id === 39">
            <div class="content-detail">
              <p class="content-title">Đơn giá bình quân căn hộ (đ/m<sup>2</sup>):</p>
              <p class="content-name">{{ formatCurrency(this.form.room_details[0].unit_price)}}đ</p>
            </div>
          </div>
          <div class="d-grid" v-if="this.form.asset_type_id !== 39">
            <div class="content-detail">
              <p class="content-title">Giá trị đất thuần còn lại (VND): </p>
              <p class="content-name">{{ formatCurrency(this.form.total_raw_amount)}}đ </p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng giá trị công trình xây dựng (VND):</p>
              <p class="content-name">{{formatCurrency(this.form.total_construction_amount)}}đ</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng giá trị tài sản khác (VND):</p>
              <p class="content-name">{{formatCurrency(this.form.total_order_amount)}}đ</p>
            </div>
          </div>
          <hr>
          <div v-if="this.form.asset_type_id !== 39">
						<div v-if="this.form.convert_fee_total > 0">
							<div>
								<h3 class="title">Chi phí chuyển đổi mục đích sử dụng đất</h3>
							</div>
							<div class="d-grid">
								<div class="content-detail">
									<p class="content-title">Tổng chi phí chuyển mục đích sử dụng đất (VND):</p>
									<p class="content-name">{{formatCurrency(this.form.convert_fee_total)}}đ</p>
								</div>
							</div>
							<h3 class="title mb-2" v-if="this.form.convert_fee_total > 0">Trong đó:</h3>
							<div class="d-grid">
								<div v-for="property in form.properties" :key="property.id">
									<div class="content-detail" v-for="property_detail in property.property_detail" :key="property_detail.id" v-if="property_detail.convert_fee > 0">
										<p class="content-title" v-if="property_detail.land_type_purpose_data !== null">Chi phí chuyển mục đích sử dụng từ {{ property_detail.land_type_purpose_data.description }} sang {{form.max_value_description}} (VND)</p>
										<p class="content-name">{{formatCurrency(property_detail.convert_fee)}}đ</p>
									</div>
								</div>
							</div>
							<hr>
						</div>
            <h3 class="title">Giá Trị QSDĐ và đơn giá đất chi tiết</h3>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Giá trị QSDĐ ước tính (VND):</p>
                <p class="content-name">{{formatCurrency(this.form.total_land_unit_price)}}đ</p>
              </div>
            </div>
            <h3 class="title mb-2">Trong đó:</h3>
            <div class="d-grid">
              <div class="content-detail" v-for="property_detail in sortedArray" :key="property_detail.id">
                <p class="content-title" >Đơn giá đất {{ property_detail.land_type_purpose_data !== undefined && property_detail.land_type_purpose_data !== null ? property_detail.land_type_purpose_data.description : ''}} (VND)</p>
                <p class="content-name">{{formatCurrency(property_detail.price_land)}}đ</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="this.form.note !== '' && this.form.note !== undefined && this.form.note !== null">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Ghi chú</h3>
            <img class="img-dropdown" :class="!showInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfo = !showInfo">
          </div>
        </div>
        <div class="card-body card-info" v-if="showInfo">
          <div class="content-detail">
            <p class="content-name">{{this.form.note}}</p>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="contain-detail">
      <div class="loading" :class="{'loading__true': isSubmit}">
        <a-spin />
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title text-nowrap">Phiên bản</h3>
              <div class="ml-2" style="width:150px">
                <InputCategoryData
                  v-model="version"
                  vid="version"
                  label="version"
                  placeholder="Chọn phiên bản"
                  :options="optionsVersion"
                  class="label-none"
                  @change="changeVersion"
                />
              </div>
            </div>
            <div v-if="form.id" class=" color_content card-status">
								{{form.id ? `TSSS_${form.id}` : 'TSSS'}}
							</div>
          </div>
        </div>
      </div>
      <div class="card">

        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title text-nowrap">Thông tin giao dịch</h3>
            <img class="img-dropdown" :class="!showInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfo = !showInfo">
          </div>
        </div>
        <div class="card-body card-info" v-if="showInfo">
          <div class="container-fluid color_content">
          <div class="row"  v-if="form.asset_type_id !== 39">
            <div class="col-12 col-lg-8" style="padding-left: 0;">
          <div class="d-grid">
            <div class="content-detail" v-if="this.form.source !== null">
              <p class="content-title">Nguồn thông tin:</p>
              <p class="content-name">{{this.form.source.description}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail" v-if="this.form.asset_type != null">
              <p class="content-title">Loại tài sản:</p>
              <p class="content-name" v-if="this.form.asset_type != null">{{this.form.asset_type.description}}</p>
              <p class="content-name" v-if="this.form.asset_type === null">Chưa có loại tài sản</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Loại giao dịch:</p>
              <p class="content-name" v-if="this.form.transaction_type !== null">{{this.form.transaction_type.description}}</p>
              <p class="content-name" v-if="this.form.transaction_type === null">Chưa có loại giao dịch</p>
            </div>
            <div class="content-detail" v-if="this.form.transaction_type !== null">
              <p class="content-title">Giá (VND):</p>
              <p class="content-name">{{formatCurrency(this.form.total_amount)}}đ</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Thời điểm đăng tin:</p>
              <p class="content-name"> {{formatDate(this.form.public_date)}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Người liên hệ</p>
              <p class="content-name">{{this.form.contact_person}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">SĐT người liên hệ:</p>
              <p class="content-name">{{this.form.contact_phone}}</p>
            </div>
          </div>
          <p class=" title" v-if="form.asset_type_id !== 39">Vị trí tài sản</p>

          <div class="d-grid" v-if="form.asset_type_id !== 39">
                <div class="content-detail">
                  <p class="content-title">Tỉnh/Thành:</p>
                  <p class="content-name">{{this.form.province !== undefined && this.form.province !== null ? this.form.province.name : ''}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Quận/Huyện:</p>
                  <p class="content-name">{{this.form.district !== undefined && this.form.district != null ? this.form.district.name : ''}}</p>
                </div>

                <div class="content-detail">
                  <p class="content-title">Phường/Xã:</p>
                  <p class="content-name">{{this.form.ward !== undefined && this.form.ward !== null ? this.form.ward.name : ''}}</p>
                </div>
          <!-- </div>
          <div class="d-grid" v-if="form.asset_type_id !== 39"> -->
                <div class="content-detail">
                  <p class="content-title">Đường:</p>
                  <p class="content-name">{{this.form.street !== undefined && this.form.street !== null ? this.form.street.name: ''}}</p>
                </div>
                <div class="content-detail" >
                  <p class="content-title">Đoạn:</p>
                  <p class="content-name">{{this.form.distance !== null && this.form.distance !== undefined ? this.form.distance.detail : 'Chưa có đoạn'}}</p>
                </div>
                <div class="content-detail"  v-if="form.asset_type_id !== 39">
							<p class="content-title">Địa hình:</p>
							<p class="content-name">{{form.topographic_data !== null && form.topographic_data !== undefined ? this.form.topographic_data.description : 'Chưa có địa hình'}}</p>
						</div>
                <!-- <div class="content-detail">
                  <p class="content-title">Tọa độ:</p>
                  <p class="content-name">{{this.form.coordinates}}</p>
                </div> -->
              </div>
          </div>
          <div class="col-12 col-lg-4">
              <div class="d-flex flex-column h-100">
                <div class="form-group-container position-relative w-100">
                  <InputText
                    id="coordinate"
                    :disabledInput="true"
                    v-model="this.form.coordinates"
                    vid="coordinates"
                    label="Tọa độ"
                    class="coordinates"
                    rules="required"
                    />
                  <!-- <div class="img-locate">
                    <img src="@/assets/icons/ic_locate.svg" alt="locate" @click="handleOpenModalMap()">
                  </div> -->
                </div>
                <!-- Map -->
                <div class="col-12 w-100 h-100 mt-3 layer-map" style="flex: 1">
              <div class="d-flex all-map w-100 h-100">
                <div class="main-map w-100 h-100">
                  <div id="mapid" class="layer-map w-100 h-100">
                      <l-map
                        ref="map_step1"
                        :zoom="map.zoom"
                        :center="map.center"
                        :maxZoom="20"
                        :options="{zoomControl: false}"
                      >
                      <l-tile-layer :url="url" :options="{ maxNativeZoom: 20, maxZoom: 20}"></l-tile-layer>
                      <l-tile-layer
                        v-for="tileProvider in tileProviders"
                        :key="tileProvider.name"
                        :name="tileProvider.name"
                        :visible="tileProvider.visible"
                        :url="tileProvider.url"
                        :attribution="tileProvider.attribution"
                        :layer-type="tileProvider.type"
                        :options="{ maxNativeZoom: 20, maxZoom: 20 }"
                      />
                        <!-- <l-tile-layer :url="url"></l-tile-layer> -->
                        <l-control-zoom position="bottomright"></l-control-zoom>

                        <l-control position="bottomleft">
                          <button class="btn btn-map" @click="handleView" type="button">
                            <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                            <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                          </button>
                        </l-control>
                        <l-control-layers position="bottomleft"></l-control-layers>
                        <l-marker :lat-lng="markerLatLng">
                          <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                          <l-tooltip>Vị trí tài sản</l-tooltip>
                        </l-marker>
                      </l-map>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!-- <div class="col-4">
            <div class="d-grid" v-if="form.asset_type_id !== 39">
              <div class="content-detail">
                  <p class="content-title">Tọa độ:</p>
                  <p class="content-name">{{this.form.coordinates}}</p>
                </div>
            </div>
          </div> -->

          </div>
          </div>
          <div class="d-grid" v-if="form.asset_type_id !== 39">
                <!-- <div class="content-detail">
                  <p class="content-title">Thửa đất số:</p>
                  <p class="content-name">{{this.form.land_no !== '' && this.form.land_no !== undefined && this.form.land_no !== null ? this.form.land_no : 'Trống'}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Bản đồ số:</p>
                  <p class="content-name">{{this.form.doc_no !== '' && this.form.doc_no !== undefined && this.form.doc_no !== null ? this.form.doc_no : 'Trống'}}</p>
                </div> -->
          </div>
          <div class="d-grid" v-if="form.asset_type_id === 39">
            <div class="content-detail">
              <p class="content-title">Tên chung cư/dự án:</p>
              <p class="content-name">{{this.form.project !== undefined && this.form.project !== null ? this.form.project.name : ''}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Block (khu):</p>
              <p class="content-name">{{this.form.block ? this.form.block.name : 'Trống'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tầng</p>
              <p class="content-name">{{this.form.floor ? this.form.floor.name : ""}}</p>
            </div>
          </div>
          <div class="d-grid" v-if="form.asset_type_id === 39">
            <div class="content-detail">
              <p class="content-title">Mã căn hộ:</p>
              <p class="content-name">{{this.form.apartment_specification ? this.form.apartment_specification.apartment_name : ""}}</p>
            </div>
          </div>
					<div class="">
						<div class="">
							<p class="content-title">Địa chỉ đầy đủ:</p>
							<p class="content-name">{{this.form.full_address !== '' && this.form.full_address !== undefined && this.form.full_address !== null ? this.form.full_address : 'Không xác định'}}</p>
						</div>

					</div>
        </div>
      </div>

      <div class="card" v-if="form.asset_type_id === 39">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Thông tin block</h3>
            <img class="img-dropdown" :class="!showBlock? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showBlock = !showBlock">
          </div>
        </div>
        <div class="card-body card-info" v-if="showBlock">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Năm xây dựng:</p>
              <p class="content-name"> {{this.form.apartment_specification.handover_year}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng số tầng:</p>
              <p class="content-name">{{this.form.apartment_specification.total_floors}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số tầng hầm:</p>
              <p class="content-name">{{this.form.apartment_specification.nb_basement}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Số tầng ở:</p>
              <p class="content-name">{{this.form.apartment_specification.nb_living_floor}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Số căn hộ mỗi tầng:</p>
              <p class="content-name" >{{this.form.apartment_specification.total_apartments}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số lượng thang máy</p>
              <p class="content-name">{{this.form.apartment_specification.nb_elevator}}</p>
            </div>
          </div>
          <p class="title title-highlight">Tiện ích cơ bản</p>
          <div class="d-grid justify-content-between container-utilities">
            <div class="col-12 text-center form-group-container d-flex" v-for="(basic_utility, index) in basic_utilities" :key="index">
              <div class=" col-12 d-flex justify-content-flex-start align-items-center">
                <label class="input-checkbox" style="margin-right: 10px;">
                  <input type="checkbox" :id="basic_utility.acronym" v-model="form.apartment_specification.utilities" :value="basic_utility.acronym" disabled>
                  <span class="check-mark"/>
                </label>
                <label class="color-black font-weight-bold mr-2 mb-2">{{basic_utility !== undefined && basic_utility !== null ? basic_utility.description : ''}}</label>

              </div>
            </div>
            <div class="col-12 col-md-4 text-center"/>
          </div>
        </div>
      </div>

      <div class="card" v-if="form.asset_type_id === 39">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Thông tin chi tiết căn hộ</h3>
            <img class="img-dropdown" :class="!showApartment? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showApartment = !showApartment">
          </div>
        </div>
        <div class="card-body card-info" v-if="showApartment">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Diện tích (m<sup>2</sup>):</p>
              <p class="content-name">{{ formatNumber(this.form.room_details[0].area)}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số phòng ngủ:</p>
              <p class="content-name">{{this.form.room_details[0].bedroom_num}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Số phòng WC:</p>
              <p class="content-name" >{{this.form.room_details[0].wc_num}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Hướng chính:</p>
              <p class="content-name">{{this.form.room_details[0].direction !== undefined && this.form.room_details[0].direction !== null ? this.form.room_details[0].direction.description : ''}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng quan chất lượng nội thất:</p>
              <p class="content-name">{{this.form.room_details[0].furniture_quality !== undefined && this.form.room_details[0].furniture_quality !== null ? this.form.room_details[0].furniture_quality.description : ''}}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card" v-if="this.form.asset_type_id !== 39">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title">Thông tin thửa đất</h3>
              <b-dropdown class="dropdown-container ml-3" no-caret>
                <template #button-content>
                  <img src="../../assets/icons/ic_more.svg" alt="">
                </template>
                <b-dropdown-item v-for ="(property) in form.properties" :key="property.id" @click="openModalDetail(property)">
                  <div class="dropdown-item-container"><img
                    src="../../assets/icons/ic_paper.svg" alt="">Tài sản đất số {{property.id}}
                  </div>
                </b-dropdown-item>
              </b-dropdown>
            </div>
              <img class="img-dropdown" :class="!showLand? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showLand = !showLand">

          </div>
        </div>
        <div class="card-body card-info card-land" v-if="showLand">
          <div class="contain-table">
            <table class="table-property">
              <thead>
              <tr>
                <th>Mã số</th>
                <th>Số tờ</th>
                <th>Số thửa</th>
                <th>Chiều rộng</th>
                <th>Chiều dài</th>
                <th>Diện tích</th>
                <th>Hình ảnh</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for = "(property) in form.properties" :key="property.id">
                <td>
                  <div class="input-code" @click="openModalDetail(property)">
                    TSD_{{property.id}}
                  </div>
                </td>
                <td>
                  <div>
                    {{property.compare_property_doc !== undefined && property.compare_property_doc !== null && property.compare_property_doc.length > 0 ? property.compare_property_doc[0].doc_num : 'Chưa có số tờ'}}
                  </div>
                </td>
                <td>
                  <div>
                    {{property.compare_property_doc[0] !== undefined && property.compare_property_doc[0] !== null && property.compare_property_doc.length > 0 ? property.compare_property_doc[0].plot_num : 'Chưa có số thửa'}}
                  </div>
                </td>
                <td>
                  {{formatNumber(property.front_side_width)}}m
                </td>
                <td>{{formatNumber(property.insight_width)}}m</td>
                <td style="white-space: nowrap" >
                  <div>
                    {{formatNumber(property.asset_general_land_sum_area)}}
                  </div>
                </td>
                <td>
                  <div class="img-contain img-contain__table" v-if="property.pic.length > 0" @click="openModalImage(property.pic[0])">
                    <img :src="property.pic[0].link" alt="pic">
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <div class="card" v-if="form.asset_type !== null && form.asset_type_id === 38 && form.tangible_assets.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Công trình xây dựng</h3>
          <img class="img-dropdown" :class="!showTable? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showTable =! showTable">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showTable">
        <div class="contain-table">
          <table class="table-property">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Loại</th>
              <th>Cấp nhà</th>
              <th>Chất lượng còn lại</th>
              <th>Diện tích sàn (m<sup>2</sup>)</th>
              <th>Giá trị ước tính (VND)</th>
              <th>Hình ảnh</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for = "(tangible_asset) in form.tangible_assets" :key="tangible_asset.id">
              <td>
                <div class="input-code" @click="openTangibleDetail(tangible_asset)">
                  TSN_{{tangible_asset.id}}
                </div>
              </td>
              <td style="white-space: nowrap">
                {{tangible_asset.building_type !== undefined && tangible_asset.building_type !== null? tangible_asset.building_type.description : ''}}
              </td>
              <td style="white-space: nowrap">
                {{tangible_asset.building_category !== undefined && tangible_asset.building_category !== null ? tangible_asset.building_category.description : ''}}
              </td>
              <td>{{ formatNumber(tangible_asset.remaining_quality) }}%</td>
              <td>{{ formatNumber(tangible_asset.total_construction_base) }}m<sup>2</sup></td>
              <td>
                {{ formatCurrency(tangible_asset.estimation_value) }}đ
              </td>
              <td>
                <div class="d-flex justify-content-center">
                  <div class="img-contain img-contain__table" v-if="tangible_asset.pic.length > 0" @click="openModalImage(tangible_asset.pic[0])">
                    <img :src="tangible_asset.pic[0].link" alt="">
                  </div>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card" v-if="form.asset_type !== null && form.asset_type_id !== 39 && form.other_assets.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Tài sản khác</h3>
          <img class="img-dropdown" :class="!showOther? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showOther = !showOther">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showOther">
        <div class="contain-table">
          <table class="table-property table-property__order">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Loại tài sản</th>
              <th>Giá trị (VND)</th>
              <th>Hình ảnh</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for = "(other_asset) in form.other_assets" :key="other_asset.id">
              <td>
                <div class="input-code" style="cursor: default">
                  PL_{{other_asset.id}}
                </div>
              </td>
              <td>
                  {{other_asset.other_asset !== undefined && other_asset.other_asset !== null ? other_asset.other_asset : ''}}
              </td>
              <td>
                {{formatCurrency(other_asset.total_amount)}}đ
              </td>
              <td>
                <div class="img-contain img-contain__table" v-for="images in other_asset.pic" :key="images.id" @click="openModalImage(images)">
                  <img :src="images.link" alt="">
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Hình ảnh</h3>
          </div>
        </div>
        <div class="card-body">
          <div class="container-img row mr-0 ml-0">
            <div class="img-empty text-center" v-if="form.pic.length === 0">
              <img src="../../assets/images/img_emply.svg" alt="empty">
              <p class="empty-content"> Chưa có hình</p>
            </div>
            <div class="img-contain col-4 col-lg-2" v-for="images in form.pic" :key="images.id" @click="openModalImage(images)">
              <img :src="images.link" alt="img">
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Ước tính đơn giá {{this.form.asset_type_id === 39? 'căn hộ' : 'đất'}}</h3>
            <img class="img-dropdown" :class="!showDeal? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showDeal = !showDeal">
          </div>
        </div>
        <div class="card-body card-info" v-if="showDeal">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Tổng giá trị (VND):</p>
              <p class="content-name">{{ formatCurrency(this.form.total_amount)}}đ</p>
            </div>
            <div class="content-detail" v-if="this.form.asset_type_id === 39">
              <p class="content-title">Diện tích (m<sup>2</sup>):</p>
              <p class="content-name">{{ formatNumber(this.form.room_details[0].area) }}m<sup>2</sup></p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Tổng giá trị {{this.form.asset_type_id === 39? 'căn hộ' : 'tài sản'}} sau điều chỉnh (VND):</p>
              <p class="content-name">{{ formatCurrency(this.form.total_estimate_amount)}}đ </p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tỉ lệ điều chỉnh (%): </p>
              <p class="content-name">{{ formatNumber(this.form.adjust_percent)}}% </p>
            </div>
            <div class="content-detail">
              <p class="content-title">Giá trị giảm/tăng (VND): </p>
              <p class="content-name">{{ formatCurrency(this.form.adjust_amount)}}đ </p>
            </div>
          </div>
          <div class="d-grid" v-if="this.form.asset_type_id === 39">
            <div class="content-detail">
              <p class="content-title">Đơn giá bình quân căn hộ (đ/m<sup>2</sup>):</p>
              <p class="content-name">{{ formatCurrency(this.form.room_details[0].unit_price)}}đ</p>
            </div>
          </div>
          <div class="d-grid" v-if="this.form.asset_type_id !== 39">
            <div class="content-detail">
              <p class="content-title">Giá trị đất thuần còn lại (VND): </p>
              <p class="content-name">{{ formatCurrency(this.form.total_raw_amount)}}đ </p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng giá trị công trình xây dựng (VND):</p>
              <p class="content-name">{{formatCurrency(this.form.total_construction_amount)}}đ</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng giá trị tài sản khác (VND):</p>
              <p class="content-name">{{formatCurrency(this.form.total_order_amount)}}đ</p>
            </div>
          </div>
          <hr>
          <div v-if="this.form.asset_type_id !== 39">
						<div v-if="this.form.convert_fee_total > 0">
							<div>
								<h3 class="title">Chi phí chuyển đổi mục đích sử dụng đất</h3>
							</div>
							<div class="d-grid">
								<div class="content-detail">
									<p class="content-title">Tổng chi phí chuyển mục đích sử dụng đất (VND):</p>
									<p class="content-name">{{formatCurrency(this.form.convert_fee_total)}}đ</p>
								</div>
							</div>
							<h3 class="title mb-2" v-if="this.form.convert_fee_total > 0">Trong đó:</h3>
							<div class="d-grid">
								<div v-for="property in form.properties" :key="property.id">
									<div class="content-detail" v-for="property_detail in property.property_detail" :key="property_detail.id" v-if="property_detail.convert_fee > 0">
										<p class="content-title" v-if="property_detail.land_type_purpose_data !== null">Chi phí chuyển mục đích sử dụng từ {{ property_detail.land_type_purpose_data.description }} sang {{form.max_value_description}} (VND)</p>
										<p class="content-name">{{formatCurrency(property_detail.convert_fee)}}đ</p>
									</div>
								</div>
							</div>
							<hr>
						</div>
            <h3 class="title">Giá Trị QSDĐ và đơn giá đất chi tiết</h3>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Giá trị QSDĐ ước tính (VND):</p>
                <p class="content-name">{{formatCurrency(this.form.total_land_unit_price)}}đ</p>
              </div>
            </div>
            <h3 class="title mb-2">Trong đó:</h3>
            <div class="d-grid">
              <div class="content-detail" v-for="property_detail in sortedArray" :key="property_detail.id">
                <p class="content-title" >Đơn giá đất {{ property_detail.land_type_purpose_data !== undefined && property_detail.land_type_purpose_data !== null ? property_detail.land_type_purpose_data.description : ''}} (VND)</p>
                <p class="content-name">{{formatCurrency(property_detail.price_land)}}đ</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="this.form.note !== '' && this.form.note !== undefined && this.form.note !== null">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Ghi chú</h3>
            <img class="img-dropdown" :class="!showInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfo = !showInfo">
          </div>
        </div>
        <div class="card-body card-info" v-if="showInfo">
          <div class="content-detail">
            <p class="content-name">{{this.form.note}}</p>
          </div>
        </div>
      </div>
    </div>
    <ModalPropertyDetail
      v-if="openDetail"
      v-bind:property="this.property"
      v-bind:img_link="this.image + '/uploads/'"
      @cancel="cancelProperty"
      :frontSideOptions="frontSideOptions"
      :twoSidesLandOptions="twoSidesLandOptions"
      :individualRoadOptions="individualRoadOptions"
    />
    <ModalTangibleDetail
      v-if="openTangible"
      v-bind:tangible="this.tangible"
      v-bind:img_link="this.image + '/uploads/'"
      @cancel="cancelTangible"
    />
    <ModalImage
      v-if="openImage"
      v-bind:image_detail ="this.image_detail.link"
      @cancel="openImage = false"
    />
    <ModalPrint
      v-if="openPrint"
      v-bind:print_detail="printDetail"
      @cancel="openPrint = false"
    />
    <div>
      <button class="btn btn-orange btn-history btn-extra" @click="showHistoryDrawer" style="position: fixed;
    top: 235px;
    right: 0px;
    z-index: 9999;">
				<img src="@/assets/icons/ic_log_history.svg" alt="history">
			</button>
      <a-drawer
				width="400"
				title="Lịch sử hoạt động"
				placement="right"
				:visible="visibleHistoryDrawer"
				@close="onHistoryDrawerClose"
        style="z-index: 99999;"
				>
					<a-timeline>
						<a-timeline-item  v-for="(item, index) in historyList" :key="index"  color="green">
							<template #dot>
								<img class="dot-image" :src="item.causer && item.causer.image ? item.causer.image : 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'" style="width: 2em" />
							</template>
							<p><strong >{{ item.causer && item.causer.name ? item.causer.name : 'Không xác	định' }}</strong></p>
							<p> {{ item.description }} </p>
							<p> {{formatDateTime(item.updated_at)}} </p>
              <p v-if="item.log_name == 'capnhat_TSSS'"> Các dữ liệu đã được thay đổi: <strong>{{ item.properties.note}}</strong> </p>
						</a-timeline-item>
					</a-timeline>
				</a-drawer>
    </div>
    <div v-if="!isMobile()" class="btn-footer d-md-flex d-block justify-content-end align-items-center">
      <div class="d-md-flex d-block button-contain ">
        <button class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">
          Trở về
        </button>
        <div v-if="edit && checkRole" class="mr-15">
          <button v-if="form.status = 2 ? 'disabled' : ''" class="btn btn-white" @click.prevent="handleEdit(form.id)">
            <img class="img" src="../../assets/icons/ic_edit.svg" alt="edit">
            Chỉnh sửa
          </button>
        </div>
        <button class="btn btn-white" @click="print(form.id)">
          <img class="img" src="../../assets/icons/ic_download.svg" alt="print">
          Tải xuống
        </button>
        <button class="btn btn-white" @click="openModalPrint(form.id)">
          <img class="img" src="../../assets/icons/ic_printer.svg" alt="print">
          In
        </button>
      </div>
    </div>
    <div v-else class="btn-footer row" style="">
      <div class="col-6">
        <button class="btn btn-white text-nowrap" @click="onCancel" type="button" style="width: fit-content;">
          <img class="img" src="../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="cancel">
          Trở về
        </button>
      </div>
      <div class="col-6"  style="text-align: right;">
        <b-dropdown class="btn_dropdown" no-caret right dropup >
          <template #button-content>
            <button style="margin-right: 2px" class="btn btn-white" type="button">
              <img class="img" src="@/assets/icons/ic_more.svg" alt="cancel">Hành động
            </button>
          </template>
          <b-dropdown-item v-if="edit && checkRole" style="margin-left: 55px;width: 150px;padding: 0;" class="btn btn-white" @click.prevent="handleEdit(form.id)">
            <div class="div_item_dropdown" v-if="form.status = 2 ? 'disabled' : ''">
              <img src="@/assets/icons/ic_edit.svg" style="margin-right: 12px; height: 1.25rem" alt="edit"/>
              <span style="font-size: 13px;">Chỉnh sửa</span>
            </div>
          </b-dropdown-item>
          <b-dropdown-item style="margin-left: 55px;width: 150px;padding: 0;" class="btn btn-white" @click="print(form.id)">
            <div class="div_item_dropdown">
              <img src="@/assets/icons/ic_download.svg" style="margin-right: 12px; height: 1.25rem" alt="print"/>
              <span style="font-size: 13px;">Tải xuống</span>
            </div>
          </b-dropdown-item>
          <b-dropdown-item style="margin-left: 55px;width: 150px;padding: 0;" class="btn btn-white" @click="openModalPrint(form.id)">
            <div class="div_item_dropdown">
              <img src="@/assets/icons/ic_printer.svg" style="margin-right: 12px; height: 1.25rem" alt="print"/>
              <span style="font-size: 13px;">In</span>
            </div>
          </b-dropdown-item>
        </b-dropdown>
      </div>

        <!-- <div v-if="edit && checkRole" class="mr-15">
          <button v-if="form.status = 2 ? 'disabled' : ''" class="btn btn-white" @click.prevent="handleEdit(form.id)">
            <img class="img" src="../../assets/icons/ic_edit.svg" alt="edit">
            Chỉnh sửa
          </button>
        </div> -->
        <!-- <button class="btn btn-white" @click="print(form.id)">
          <img class="img" src="../../assets/icons/ic_download.svg" alt="print">
          Tải xuống
        </button>
        <button class="btn btn-white" @click="openModalPrint(form.id)">
          <img class="img" src="../../assets/icons/ic_printer.svg" alt="print">
          In
        </button> -->
      </div>
    </div>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import '../../../node_modules/leaflet/dist/leaflet.css';
</style>
<script>
import InputText from '@/components/Form/InputText'
import InputCategoryData from '@/components/Form/InputCategoryData'
import WareHouse from '@/models/WareHouse'
import ModalPropertyDetail from '@/components/Modal/ModalPropertyDetail'
import ModalImage from '@/components/Modal/ModalImage'
import ModalPrint from '@/components/Modal/ModalPrint'
import ModalTangibleDetail from '@/components/Modal/ModalTangibleDetail'
import {BDropdown, BDropdownItem} from 'bootstrap-vue'
import moment from 'moment'
import {LMap, LControlZoom, LTileLayer, LMarker, LTooltip, LIcon, LControl, LControlLayers} from 'vue2-leaflet'

export default {
	name: 'Detail',
	components: {
		ModalPrint,
		InputText,
		ModalPropertyDetail,
		ModalTangibleDetail,
		ModalImage,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		InputCategoryData,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LControlLayers,
		LIcon
	},
	data () {
		return {
			imageMap: true,
			location: {
				lng: '',
				lat: ''
			},
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			url: 'https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff',
			tileProviders: [
				{
					name: 'Bản đồ ranh tờ, thửa',
					visible: true,
					url: 'https://cdn.estatemanner.com/tile/ranh_thua/{z}/{x}/{y}.png',
					attribution: '© Fastvalue',
					type: 'overlay'
				},
				{
					name: 'Bản đồ thông tin quy hoạch',
					visible: false,
					attribution: '© Fastvalue',
					url: 'https://cdn.estatemanner.com/tile/qhsdd/{z}/{x}/{y}.png',
					type: 'overlay'
				},
				{
					name: 'Bản đồ quy hoạch lộ giới',
					visible: false,
					attribution: '© Fastvalue',
					url: 'https://cdn.estatemanner.com/tile/qhlg/{z}/{x}/{y}.png',
					type: 'overlay'
				}
			],
			visibleHistoryDrawer: false,
			historyList: [],
			version: '',
			versions: [],
			frontSideOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			twoSidesLandOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			individualRoadOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			checkRole: true,
			output: null,
			openImage: false,
			openDetail: false,
			openTangible: false,
			showInfo: true,
			showTable: true,
			showLand: true,
			showOther: true,
			showDeal: true,
			showBlock: true,
			showApartment: true,
			openPrint: false,
			isSubmit: false,
			image: '',
			printDetail: '',
			purpose_use_lands: [],
			basic_utilities: [],
			checkedId: [],
			form: {
				id: '',
				id_amount: '',
				status: '1',
				input_source: 'DONAVA',
				post_id: '',
				asset_type: {
					type: ''
				},
				created_by: '',
				province: '',
				district: '',
				ward: '',
				street: '',
				doc_num: '',
				ward_id: '',
				street_id: '',
				transaction_type: '',
				land_no: '',
				doc_no: '',
				full_address: '',
				coordinates: '',
				source: '',
				public_date: '',
				property_other: '',
				contact_phone: '',
				total_area: '1',
				total_construction_area: '1',
				total_construction_amount: '',
				total_amount: '1',
				total_area_amount: '1',
				average_land_unit_price: '1',
				contact_person: '',
				properties: [],
				tangible_assets: [],
				other_assets: [],
				pic: [
					{
						link: ''
					}
				]
			},
			edit: false
		}
	},
	// async mounted () {
	// 	if (this.$refs.map_step1 && this.$refs.map_step1.mapObject) {
	// 		this.$refs.map_step1.mapObject.invalidateSize()
	// 	}

	// 	await this.initMap()
	// },
	async created () {
		if ('id' in this.$route.query && this.$route.name === 'warehouse.detail') {
			if (this.$route.meta['detail']) {
				this.form = Object.assign(this.form, {
					...this.$route.meta['detail']
				})
				await this.getVersion()
				this.version = this.versions[this.versions.length - 1].version
				this.changeVersion()
				await this.initMap()
			} else {
				this.$router.push({name: 'page-not-found'})
			}
		} else {
		}
		this.getProfiles()
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
		})
	},
	computed: {
		sortedArray: function () {
			function compare (a, b) {
				if (a.price_land > b.price_land) { return -1 }
				if (a.price_land < b.price_land) { return 1 }
				return 0
			}
			// eslint-disable-next-line vue/no-side-effects-in-computed-properties
			return this.purpose_use_lands.sort(compare)
		},
		optionsVersion () {
			return {
				data: this.versions,
				id: 'version',
				key: 'version'
			}
		}
	},
	methods: {
		isMobile () {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true
			} else {
				return false
			}
		},
		async initMap () {
			// eslint-disable-next-line no-undef
			// console.log('form', this.form)
			if (this.form.coordinates) {
				this.map.center = [this.form.coordinates.split(',')[0], this.form.coordinates.split(',')[1]]
				this.markerLatLng = [this.form.coordinates.split(',')[0], this.form.coordinates.split(',')[1]]
				this.map.zoom = 17
				// // console.log('vô đây', map)
			} else {
				this.markerLatLng = [10.964112, 106.856461]
				this.map.center = [10.964112, 106.856461]
			}
		},
		handleView () {
			if (this.url === 'https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff') {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url = 'https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff'
				this.imageMap = false
			} else {
				this.url = 'https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff'
				this.imageMap = true
			}
		},
		formatDateTime (value) {
			return moment(String(value)).format('HH:mm DD/MM/YYYY')
		},
		async getHistoryTimeline (id) {
			const res = await WareHouse.getHistoryTimeline(id)
			if (res.data) {
				this.historyList = res.data
				// console.log('history_list', this.historyList)
			} else if (res.error) {
				return this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 5000
				})
			}
		},
		onHistoryDrawerClose () {
			this.visibleHistoryDrawer = false
		},
		showHistoryDrawer () {
			this.visibleHistoryDrawer = true
			this.getHistoryTimeline(this.form.id)
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
			if (this.form.created_by && profile.data.user.roles[0].pivot.model_id === this.form.created_by.id) {
				this.checkRole = true
			}
		},
		cancelTangible () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openTangible = false
		},
		cancelProperty () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openDetail = false
		},
		async print (id) {
			this.isSubmit = true
			await WareHouse.getPrint(id).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				}
				this.isSubmit = false
			}
			)
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatCurrency (value) {
			if (value) {
				let num = (value / 1).toFixed(0).replace('.', ',')
				return num.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
			return value
		},
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
			return num
		},
		openModalImage (data) {
			this.openImage = true
			this.image_detail = data
		},
		async openModalPrint (data) {
			this.isSubmit = true
			await this.getPrint(data)
			this.openPrint = true
			this.isSubmit = false
		},
		openModalDetail (data) {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openDetail = true
			this.property = data
			if (data.front_side === 1) {
				this.frontSideOptions.items.preSelected = 'Yes'
				data.front_side_switch = true
			} else if (data.front_side === 0) {
				data.front_side_switch = false
				this.frontSideOptions.items.preSelected = 'No'
			} else {
				this.frontSideOptions.items.preSelected = ''
			}
			if (data.two_sides_land === true) {
				this.twoSidesLandOptions.items.preSelected = 'Yes'
			} else if (data.two_sides_land === false) {
				this.twoSidesLandOptions.items.preSelected = 'No'
			} else {
				this.twoSidesLandOptions.items.preSelected = ''
			}
			if (data.individual_road === 1) {
				this.individualRoadOptions.items.preSelected = 'Yes'
			} else if (data.individual_road === 0) {
				this.individualRoadOptions.items.preSelected = 'No'
			} else {
				this.individualRoadOptions.items.preSelected = ''
			}
		},
		openTangibleDetail (data) {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openTangible = true
			this.tangible = data
		},
		onCancel () {
			return this.$router.push({name: 'warehouse.index'})
		},
		sortArrayPropertyDetail () {
			let purpose_use_lands = []
			this.form.properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					purpose_use_lands.push(property_detail)
				})
				this.purpose_use_lands = purpose_use_lands
			})
		},
		async getPrint (id) {
			const resp = await WareHouse.getPrintPdf(id)
			this.printDetail = resp.data.url
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.basic_utilities = [...reps.data.tien_ich_co_ban]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async handleEdit (id) {
			this.$router.push({
				name: 'warehouse.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		},
		handleCheck () {
			if (this.form.block_specification !== undefined && this.form.block_specification.length > 0) {
				this.form.apartment_specification.basic_utilities.forEach(item => {
					this.checkedId.push(
						item.id
					)
				})
			}
		},
		async changeVersion () {
			this.isSubmit = true
			const res = await WareHouse.getAssetGeneralDetailVersion(this.form.id, this.version)
			this.form = res.data
			this.isSubmit = false
		},
		async getVersion () {
			const res = await WareHouse.getVersion(this.form.id)
			this.versions = [...res.data]
		}
	},
	mounted () {
		this.image = process.env.API_URL
	},
	beforeRouteEnter: async (to, from, next) => {
		const warehouse = await WareHouse.find(to.query['id'])
		to.meta['detail'] = warehouse.data
		return next()
	},
	beforeMount () {
		this.sortArrayPropertyDetail()
		this.getDictionary()
		this.handleCheck()
	}
}
</script>
<style scoped lang="scss">
    /deep/ .dropdown-item {
    min-width: unset!important;
    // padding: 0!important;
  }

  // /deep/ .dropdown-menu.show {
  //   background: transparent!important;
  //   box-shadow: none!important;
  // }

  // /deep/ .dropdown-menu-right.show {
  //   background: transparent!important;
  //   box-shadow: none!important;
  // }

  // /deep/ .dropdown-menu {
  //   background: transparent!important;
  //   box-shadow: none!important;
  // }

  /deep/ .dropup .dropdown-menu {
    background: transparent!important;
    box-shadow: none!important;
  }

.contain-detail {
  margin-bottom: 80px;
  @media (max-width: 767px) {
    margin-bottom: 145px;
  }
}
.pannel {
  background: #FFFFFF;
  box-shadow: 1px 2px 0 #e5eaee;
  border-radius: 5px;
  padding: 25px;
  margin-bottom: 40px;

  &__table {
    padding: 25px 0;
    border-radius: 5px;
  }

  &__input {
    p {
      color: #5a5386;
      font-weight: 600;
    }
  }
}
.button-contain {
  @media (max-width: 418px) {
    display: flex !important;
    justify-content: space-between;
    flex-wrap: wrap;
  }
}
.img{
  margin-right: 13px;
  max-width: 20px;
}
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6;
    }
    @media (max-width: 768px) {
      padding: 12px;
    }
    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body {

    @media (max-width: 787px) {
      padding: 15px;
    }
  }
  &-sub_header_title {
    padding: 15px 24px;
  }
  &-info {
    .title {
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;

      &-highlight {
        background: rgba(252, 194, 114, 0.53);
        text-align: center;
        padding: 10px 0;
        border-radius: 2px;
      }
    }
  }
  &-land {
    position: relative;
    padding: 0;
  }
}
.btn{
  &-white{
    max-height: none;

    line-height: 19.07px;
    margin-right: 15px;

    @media (max-width: 418px) {
      width: 45%;
      margin-right: 0;
    }
    &:last-child{
      margin-right: 0;
    }
  }
  &-contain{
    margin-bottom: 55px;
    @media (max-width: 418px) {
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
    }
  }
}
.d-grid{
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  grid-column-gap: 8.9%;
  @media (max-width: 767px) {
    grid-template-columns: 1fr;
  }
}
.content{
  &-detail{
    p {
      padding-right: 10px;
      &:nth-last-child(1) {
        padding-right: 0;
      }
    }
  }
  &-title{
    color: #555555;
    margin-bottom: 5px;

    font-weight: 500;
  }
  &-name{
    font-size: 1.125rem;
    color: #000000;
    margin-bottom: 15px;
    font-weight: 600;
    @media (max-width: 767px) {

    }
    &__code{
      color: #FAA831;
    }
  }
}
.title{
  margin-bottom: 35px;
}
.contain-table{
    overflow-x: auto;
  @media (max-width: 767px) {
    overflow-y: hidden;
  }
  .table-property{
    width: 100%;
  }
}
.table-property{
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px 0;
      font-weight: 500;
      @media (max-width: 418px) {
        padding: 12px;
      }
    }
  }
  tbody{
    td{
      border: 1px solid #E5E5E5;
      &:first-child{
        border-left: none;
        width: 180px
      }
      &:last-child{
        border-right: none;
      }
      box-sizing: border-box;
      padding: 14px;
    }
  }
}
.img-content{
  padding-left: 20px;
  color: rgba(0,0,0,0.85);
  font-size: 1.125rem;
  font-weight: 600;
  span{
    font-weight: 500;
    margin-left: 10px;
  }
}
.input-code{
  color: #FAA831;
  cursor: pointer;
}
.img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
    transform: rotate(90deg);
    transition: .3s;
  }
}
.img-contain {
  aspect-ratio: 1/1;
  overflow: hidden;
  img{
    cursor: pointer;
    object-fit: cover;
    height: 100%;
  }
  &__table{
    margin: auto;
    max-width: 50px;
    max-height: 50px;
    img{
      object-fit: cover;
      object-position: top;
      cursor: pointer;
      display: flex;
      justify-content: center;
      max-width: 50px;
      max-height: 50px;
    }
  }
}
.product-images {
  @media (max-width: 786px) {
    display: block !important;
    .img-contain {
      margin-bottom: 5px;
      max-width: 100%;
      max-height: 100%;
      img {
        width: 100%;
        max-width: 100%;
        max-height: 100%;
      }
    }
  }
}
.container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
}
.btn-footer {
  background: #FFFFFF;
  padding: 20px 30px;
  position: fixed;
  left: 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  bottom: 0;
  right: 0;
}
.dropdown-container{
  border-radius: 2px;
  background: #FAA831;
  img{
    padding: 7px;
  }
}
.loading{
  display: none;
  &__true{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100dvh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading{
      &:after{
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
.input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  input {
    width: 20px;
    height: 20px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #FAA831;
        &:after {
          display: block;
        }
      }
    }
  }
  .check-mark {
    position: absolute;
    top: 0;
    left: 0;
    cursor: pointer;
    width: 20px;
    height: 20px;
    // background-color: #FFFFFF;
    border: 1px solid #FAA831;
    border-radius: 4px;
    &:after {
      content: "";
      position: absolute;
      display: none;
      left: 50%;
      top: 50%;
      width: 5px;
      height: 10px;
      border: solid #FFFFFF;
      border-width: 0 3px 3px 0;
      -webkit-transform: rotate(45deg) translate(-125%, -25%);
      -ms-transform: rotate(45deg) translate(-125%, -25%);
      transform: rotate(45deg) translate(-125%, -25%);
    }
  }
}
.mr-15{
  margin-right: 15px;
  @media (max-width: 767px) {
    margin-right: 0;
  }
}
.card-status {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #FFFFFF;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;

	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}

.form-group-container {
  margin-top: 10px;
}
.main-map {
  position: relative;
  height: 100%;
  width: 100%;
  transition-timing-function: ease;
  transition-duration: 0.25s;
  overflow-x: hidden;
  @media (max-width: 1023px) {
    width: 100%;
  }
  .layer-map {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 0;
    transition-timing-function: ease;
    transition-duration: 0.25s;

  }
  // map
  .btn-map {
  background: #FFFFFF;
  border-radius: 5px;
  border: 3px solid #FFFFFF;
  padding: 0;
  box-sizing: border-box;
  img{
    max-width: 50px;
    height: auto;
  }
}

}
</style>
