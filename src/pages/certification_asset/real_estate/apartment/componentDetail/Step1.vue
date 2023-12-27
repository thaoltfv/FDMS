<template>
  <div v-if="!isMobile()">
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin chung về tài sản thẩm định</h3>
          <img class="img-dropdown" :class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailAppraise = !showCardDetailAppraise">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailAppraise">
        <div class="container-fluid color_content">
          <div class="row">
            <div class="col-12 col-lg-7">
              <div class="row">
                <!-- <div class="col-md-12 col-lg-6">
                  <InputCategory
                    v-model="data.asset_type_id"
                    vid="asset_type_id"
                    label="Loại tài sản"
                    rules="required" class="form-group-container"
                    :options="optionsType"
                    :disabled="true"
                    @change="changeAssetType($event)" />
                </div> -->

                <div class="col-md-12 col-lg-6">
                  <InputCategory
                    v-model="data.province_id"
                    vid="province_id"
                    label="Tỉnh/Thành"
                    rules="required"
                    class="form-group-container"
                    :disabled="true"
                    :options="optionsProvince"
                    @change="changeProvince($event)"
                  />
                </div>
                <div class="col-md-12 col-lg-6">
                  <InputCategory
                    v-model="data.district_id"
                    vid="district_id"
                    label="Quận/Huyện"
                    rules="required"
                    :disabled="true"
                    class="form-group-container"
                    @change="changeDistrict($event)"
                    :options="optionsDistrict"
                  />
                </div>
                <div class="col-md-12">
                  <InputCategory
										v-model="data.project_id"
										vid="project_id"
										rules="required"
										label="Tên chung cư"
										class="form-group-container"
										:options="optionsProjects"
                    :disabled="true"
										@change="handleChangeProject"
									/>
                </div>
                <div class="col-md-12 col-lg-6">
                  <InputCategory
                    v-model="data.ward_id"
                    vid="ward_id"
                    label="Phường/Xã"
                    :disabled="true"
                    class="form-group-container"
                    :options="optionsWard"
                    @change="changeWard($event)"
                  />
                </div>
                <div class="col-md-12 col-lg-6">
                  <InputCategory
                    v-model="data.street_id"
                    vid="street_id"
                    label="Đường/Phố"
                    :disabled="true"
                    class="form-group-container"
                    @change="changeStreet($event)"
                    :options="optionsStreet"
                  />
                </div>
                <div class="col-12">
                  <InputText
                    v-model="data.full_address"
                    vid="full_address"
                    label="Địa chỉ"
                    rules="required"
                    :disabledInput="true"
                    class="form-group-container"
                  />
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-5">
              <div class="d-flex flex-column h-100">
                <div class="form-group-container position-relative w-100">
                  <InputText
                    id="coordinate"
                    :disabledInput="true"
                    v-model="data.coordinates"
                    vid="coordinates"
                    label="Tọa độ"
                    class="coordinates"
                    rules="required"
                    />
                </div>
                <!-- Map -->
                <div class="col-12 w-100 h-100 mt-3 d-none d-lg-block layer-map" style="flex: 1">
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
                        <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                        <l-control-zoom position="bottomright"></l-control-zoom>
                        <l-control position="bottomleft">
                          <button class="btn btn-map" @click="handleView" type="button">
                            <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                            <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                          </button>
                        </l-control>
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

          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Chi tiết căn hộ</h3>
          <img class="img-dropdown" :class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailTraffic = !showCardDetailTraffic">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailTraffic">
        <div class="container-fluid">
          <div class="row">
            <InputCategory
              v-model="data.apartment_asset_properties.block_id"
              vid="block_id"
              rules="required"
              label="Block (khu)"
              class="col-12 col-lg-4 form-group-container"
              :options="optionsBlocks"
              :disabled="true"
              @change="handleChangeBlock"
            />
            <InputCategory
              v-model="data.apartment_asset_properties.floor_id"
              vid="floor_id"
              rules="required"
              label="Tầng"
              class="col-12 col-lg-4 form-group-container"
              :options="optionsFloors"
              @change="handleChangeFloor"
              :disabled="true"
            />
            <InputText
                v-model="data.apartment_asset_properties.apartment_name"
                vid="apartment_name"
                label="Mã căn hộ"
                class="col-12 col-lg-4 form-group-container"
                :disabledInput="true"
            />
            <InputArea
              v-model="data.apartment_asset_properties.area"
              vid="area"
              label="Diện tích (m²)"
              rules="required"
              :max="99999999"
              @change="handleArea($event)"
              :disabled="true"
              class="col-12 col-lg-4 form-group-container"
            />
            <InputNumberNoneFormat
              v-model="data.apartment_asset_properties.bedroom_num"
              vid="bedroom_num"
              label="Số phòng ngủ"
              rules="required"
              :max="9999"
              :disabled="true"
              @change="handleBedroomNum($event)"
              class="col-12 col-lg-4 form-group-container"
            />
            <InputNumberNoneFormat
              v-model="data.apartment_asset_properties.wc_num"
              vid="wc_num"
              label="Số phòng WC"
              rules="required"
              :max="9999"
              @change="handleWCNum($event)"
              :disabled="true"
              class="col-12 col-lg-4 form-group-container"
            />
            <InputCategory
              v-model="data.apartment_asset_properties.handover_year"
              class="col-12 col-lg-4 form-group-container"
              vid="handover_year"
              label="Năm sử dụng"
              rules="required"
              @change="changeUsingYear"
              :options="optionYearBuild"
              :disabled="true"
            />
            <InputCategory
              v-model='data.apartment_asset_properties.direction_id'
              vid="direction_id"
              label="Hướng chính"
              rules="required"
              :disabled="true"
              class="col-12 col-lg-4 form-group-container"
              :options="optionDirection"
            />
            <InputCategory
              v-model='data.apartment_asset_properties.furniture_quality_id'
              vid="furniture_quality_id"
              label="Tình trạng nội thất"
              rules="required"
              :disabled="true"
              class="col-12 col-lg-4 form-group-container"
              :options="optionFurniture"
            />
            <InputText
                v-model="data.appraise_asset"
                vid="data.appraise_asset"
                label="Tên căn hộ"
                rules="required"
                class="col-12 col-lg-8 form-group-container"
                :disabledInput="true"
            />
            <InputCategory
              v-model='data.apartment_asset_properties.loai_can_ho_id'
              vid="loai_can_ho_id"
              label="Loại căn hộ"
              rules="required"
              class="col-12 col-lg-4 form-group-container"
              :options="optionsLoaiCanHo"
              :disabled="true"
            />
            <InputTextarea
              label="Mô tả"
              v-model="data.apartment_asset_properties.description"
              vid="description"
              :rows="4"
              :maxLength="1000"
              :disableInput="true"
              class="col-12 form-group-container"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Tiện ích nội khu</h3>
          <img class="img-dropdown" :class="!showCardDetailEconomicAndSocial ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailEconomicAndSocial = !showCardDetailEconomicAndSocial">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailEconomicAndSocial">
        <div class="container-fluid">
          <div class="row justify-content-flex-start container-utilities">
            <div class="col-12 col-md-6 text-center form-group-container d-flex" v-for="(basic_utility, index) in basic_utilities" :key="index">
              <div class="col d-flex justify-content-flex-start align-items-center">
                <label class="input-checkbox" style="margin-right: 10px;">
                  <input type="checkbox" :disabled="true" :id="basic_utility.id" :value="basic_utility.acronym" v-model="data.apartment_asset_properties.utilities" >
                  <span class="check-mark"/>
                </label>
                <label :for="basic_utility.id" style="cursor:pointer" class="color-black font-weight-bold mr-2 mb-2">{{basic_utility.description}}</label>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
		<div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin quy hoạch</h3>
          <img class="img-dropdown" :class="!showDetailPlanning ? 'img-dropdown__hide' : ''"
               src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showDetailPlanning = !showDetailPlanning">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailPlanning">
        <div class="container-fluid">
          <div class="row">
						<div class="col-12 col-lg-6">
							<InputTextarea
								v-model="data.real_estate.planning_info"
								label="Thông tin quy hoạch"
								class="form-group-container"
								:disableInput="true"
                :autosize="true"
							/>
						</div>
						<div class="col-12 col-lg-6">
							<InputTextarea
								v-model="data.real_estate.planning_source"
								label="Nguồn thông tin"
								class="form-group-container"
								:disableInput="true"
                :autosize="true"
							/>
						</div>
						<div class="col-12 col-lg-6">
							<InputText
								v-model="data.real_estate.contact_person"
								label="Người hướng dẫn khảo sát"
								class="form-group-container"
								:disabledInput="true"
							/>
						</div>
						<div class="col-12 col-lg-6">
							<InputText
								v-model="data.real_estate.contact_phone"
								label="Số điện thoại"
								class="form-group-container"
								:disabledInput="true"
							/>
						</div>
          </div>
        </div>
      </div>
    </div>
     <div class="card">
      <div class="card-title d-flex align-items-center justify-content-between">
        <form enctype="multipart/form-data" class="d-flex align-items-center">
          <h3 class="title">Hình ảnh</h3>
        </form>
        <img class="img-dropdown" :class="!showCardDetailImage ? 'img-dropdown__hide' : ''"
          src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailImage = !showCardDetailImage">
      </div>
      <div class="card-body" v-show="showCardDetailImage">
        <Tabs class="tab_contruction" :theme="theme" :navAuto="true">
          <TabItem name="Đường tiếp giáp tài sản">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageType && data.pic.filter(i => i.type_id === imageType.id).length > 0" class="container-img row mr-0 ml-0">
                <div class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imageType.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh đường tiếp giáp tài sản
              </div>
            </div>
          </TabItem>
          <TabItem name="Tổng thể tài sản">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imgOverall && data.pic.filter(i => i.type_id === imgOverall.id).length > 0" class="container-img row mr-0 ml-0" >
                <div class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imgOverall.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                  <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                  </svg>
                  Không có hình ảnh tổng thể tài sản
              </div>
            </div>
          </TabItem>
          <TabItem name="Hiện trạng tài sản">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageCurrentStatus && data.pic.filter(item => item.type_id === imageCurrentStatus.id).length > 0" class="container-img row mr-0 ml-0" >
                <div class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imageCurrentStatus.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh hiện trạng tài sản
              </div>
            </div>
          </TabItem>
          <TabItem name="Khác">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageJuridical && data.pic.filter(i => i.type_id === imageJuridical.id).length > 0" class="container-img row mr-0 ml-0">
                <div class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imageJuridical.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh khác
              </div>
            </div>
          </TabItem>
        </Tabs>
      </div>
    </div>
    <ModalMap
      v-if="openModalMap"
      @cancel="openModalMap = false"
      :location="location"
      :address="full_address_street"
      :center_map="data.coordinates"
      @action="handleCoordinates"
    />
  </div>
  <div v-else>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin chung về tài sản thẩm định</h3>
          <img class="img-dropdown" :class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailAppraise = !showCardDetailAppraise">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailAppraise">
        <div class="container-fluid color_content">
          <div class="row">
            <div class="col-12 col-lg-7" style="padding:0;">
              <div class="row">
                <!-- <div class="col-md-12 col-lg-6">
                  <InputCategory
                    v-model="data.asset_type_id"
                    vid="asset_type_id"
                    label="Loại tài sản"
                    rules="required" class="form-group-container"
                    :options="optionsType"
                    :disabled="true"
                    @change="changeAssetType($event)" />
                </div> -->

                <div class="col-6">
                  <InputCategory
                    v-model="data.province_id"
                    vid="province_id"
                    label="Tỉnh/Thành"
                    rules="required"
                    class="form-group-container"
                    :disabled="true"
                    :options="optionsProvince"
                    @change="changeProvince($event)"
                  />
                </div>
                <div class="col-6">
                  <InputCategory
                    v-model="data.district_id"
                    vid="district_id"
                    label="Quận/Huyện"
                    rules="required"
                    :disabled="true"
                    class="form-group-container"
                    @change="changeDistrict($event)"
                    :options="optionsDistrict"
                  />
                </div>
                <div class="col-md-12">
                  <InputCategory
										v-model="data.project_id"
										vid="project_id"
										rules="required"
										label="Tên chung cư"
										class="form-group-container"
										:options="optionsProjects"
                    :disabled="true"
										@change="handleChangeProject"
									/>
                </div>
                <div class="col-6">
                  <InputCategory
                    v-model="data.ward_id"
                    vid="ward_id"
                    label="Phường/Xã"
                    :disabled="true"
                    class="form-group-container"
                    :options="optionsWard"
                    @change="changeWard($event)"
                  />
                </div>
                <div class="col-6">
                  <InputCategory
                    v-model="data.street_id"
                    vid="street_id"
                    label="Đường/Phố"
                    :disabled="true"
                    class="form-group-container"
                    @change="changeStreet($event)"
                    :options="optionsStreet"
                  />
                </div>
                <div class="col-12">
                  <InputText
                    v-model="data.full_address"
                    vid="full_address"
                    label="Địa chỉ"
                    rules="required"
                    :disabledInput="true"
                    class="form-group-container"
                  />
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-5" style="padding:0;">
              <div class="d-flex flex-column h-100">
                <div class="form-group-container position-relative w-100">
                  <InputText
                    id="coordinate"
                    :disabledInput="true"
                    v-model="data.coordinates"
                    vid="coordinates"
                    label="Tọa độ"
                    class="coordinates"
                    rules="required"
                    />
                </div>
                <!-- Map -->
                <div class="col-12 mt-3 layer-map" >
              <div class="d-flex all-map " style="padding: 0; height: 40vh;margin-top: 10px;">
                <div class="main-map ">
                  <div id="mapid" class="layer-map">
                      <l-map
                        ref="map_step1"
                        :zoom="map.zoom"
                        :center="map.center"
                        :maxZoom="20"
                        :options="{zoomControl: false}"
                      >
                        <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                        <l-control-zoom position="bottomright"></l-control-zoom>
                        <l-control position="bottomleft">
                          <button class="btn btn-map" @click="handleView" type="button">
                            <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                            <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                          </button>
                        </l-control>
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

          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Chi tiết căn hộ</h3>
          <img class="img-dropdown" :class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailTraffic = !showCardDetailTraffic">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailTraffic" style="padding-left: 0;padding-right: 0;">
        <div class="container-fluid">
          <div class="row">
            <InputCategory
              v-model="data.apartment_asset_properties.block_id"
              vid="block_id"
              rules="required"
              label="Block (khu)"
              class="col-6 form-group-container"
              :options="optionsBlocks"
              :disabled="true"
              @change="handleChangeBlock"
            />
            <InputCategory
              v-model="data.apartment_asset_properties.floor_id"
              vid="floor_id"
              rules="required"
              label="Tầng"
              class="col-6 form-group-container"
              :options="optionsFloors"
              @change="handleChangeFloor"
              :disabled="true"
            />
            <InputText
                v-model="data.apartment_asset_properties.apartment_name"
                vid="apartment_name"
                label="Mã căn hộ"
                class="col-6 form-group-container"
                :disabledInput="true"
            />
            <InputArea
              v-model="data.apartment_asset_properties.area"
              vid="area"
              label="Diện tích (m²)"
              rules="required"
              :max="99999999"
              @change="handleArea($event)"
              :disabled="true"
              class="col-6 form-group-container"
            />
            <InputNumberNoneFormat
              v-model="data.apartment_asset_properties.bedroom_num"
              vid="bedroom_num"
              label="Số phòng ngủ"
              rules="required"
              :max="9999"
              :disabled="true"
              @change="handleBedroomNum($event)"
              class="col-6 form-group-container"
            />
            <InputNumberNoneFormat
              v-model="data.apartment_asset_properties.wc_num"
              vid="wc_num"
              label="Số phòng WC"
              rules="required"
              :max="9999"
              @change="handleWCNum($event)"
              :disabled="true"
              class="col-6 form-group-container"
            />
            <InputCategory
              v-model="data.apartment_asset_properties.handover_year"
              class="col-6 form-group-container"
              vid="handover_year"
              label="Năm sử dụng"
              rules="required"
              @change="changeUsingYear"
              :options="optionYearBuild"
              :disabled="true"
            />
            <InputCategory
              v-model='data.apartment_asset_properties.direction_id'
              vid="direction_id"
              label="Hướng chính"
              rules="required"
              :disabled="true"
              class="col-6 form-group-container"
              :options="optionDirection"
            />
            <InputCategory
              v-model='data.apartment_asset_properties.furniture_quality_id'
              vid="furniture_quality_id"
              label="Tình trạng nội thất"
              rules="required"
              :disabled="true"
              class="col-6 form-group-container"
              :options="optionFurniture"
            />
            <InputText
                v-model="data.appraise_asset"
                vid="data.appraise_asset"
                label="Tên căn hộ"
                rules="required"
                class="col-6 form-group-container"
                :disabledInput="true"
            />
            <InputCategory
              v-model='data.apartment_asset_properties.loai_can_ho_id'
              vid="loai_can_ho_id"
              label="Loại căn hộ"
              rules="required"
              class="col-6 form-group-container"
              :options="optionsLoaiCanHo"
              :disabled="true"
            />
            <InputTextarea
              label="Mô tả"
              v-model="data.apartment_asset_properties.description"
              vid="description"
              :rows="4"
              :maxLength="1000"
              :disableInput="true"
              class="col-12 form-group-container"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Tiện ích nội khu</h3>
          <img class="img-dropdown" :class="!showCardDetailEconomicAndSocial ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailEconomicAndSocial = !showCardDetailEconomicAndSocial">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailEconomicAndSocial" style="padding-left: 0;padding-right: 0;">
        <div class="container-fluid">
          <div class="row justify-content-flex-start container-utilities">
            <div style="padding-right: 0;" class="col-6 text-center form-group-container d-flex" v-for="(basic_utility, index) in basic_utilities" :key="index">
              <div class="col d-flex justify-content-flex-start align-items-center">
                <label class="input-checkbox" style="margin-right: 10px;">
                  <input type="checkbox" :disabled="true" :id="basic_utility.id" :value="basic_utility.acronym" v-model="data.apartment_asset_properties.utilities" >
                  <span class="check-mark"/>
                </label>
                <label :for="basic_utility.id" style="cursor:pointer;font-size: 14px;" class="color-black font-weight-bold mr-2 mb-2">{{basic_utility.description}}</label>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
		<div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin quy hoạch</h3>
          <img class="img-dropdown" :class="!showDetailPlanning ? 'img-dropdown__hide' : ''"
               src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showDetailPlanning = !showDetailPlanning">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailPlanning" style="padding-left: 0;padding-right: 0;">
        <div class="container-fluid">
          <div class="row">
						<div class="col-6">
							<InputTextarea
								v-model="data.real_estate.planning_info"
								label="Thông tin quy hoạch"
								class="form-group-container"
								:disableInput="true"
                :autosize="true"
							/>
						</div>
						<div class="col-6">
							<InputTextarea
								v-model="data.real_estate.planning_source"
								label="Nguồn thông tin"
								class="form-group-container"
								:disableInput="true"
                :autosize="true"
							/>
						</div>
						<div class="col-6">
							<InputText
								v-model="data.real_estate.contact_person"
								label="Người HD khảo sát"
								class="form-group-container"
								:disabledInput="true"
							/>
						</div>
						<div class="col-6">
							<InputText
								v-model="data.real_estate.contact_phone"
								label="Số điện thoại"
								class="form-group-container"
								:disabledInput="true"
							/>
						</div>
          </div>
        </div>
      </div>
    </div>
     <div class="card">
      <div class="card-title d-flex align-items-center justify-content-between">
        <form enctype="multipart/form-data" class="d-flex align-items-center">
          <h3 class="title">Hình ảnh</h3>
        </form>
        <img class="img-dropdown" :class="!showCardDetailImage ? 'img-dropdown__hide' : ''"
          src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailImage = !showCardDetailImage">
      </div>
      <div class="card-body" v-show="showCardDetailImage" style="padding-left: 0;padding-right: 0;">
        <Tabs class="tab_contruction" :theme="theme" :navAuto="true">
          <TabItem name="Đường tiếp giáp tài sản">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageType && data.pic.filter(i => i.type_id === imageType.id).length > 0" class="container-img row mr-0 ml-0">
                <div style="width: auto;" class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imageType.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh đường tiếp giáp tài sản
              </div>
            </div>
          </TabItem>
          <TabItem name="Tổng thể tài sản">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imgOverall && data.pic.filter(i => i.type_id === imgOverall.id).length > 0" class="container-img row mr-0 ml-0" >
                <div style="width: auto;" class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imgOverall.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                  <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                  </svg>
                  Không có hình ảnh tổng thể tài sản
              </div>
            </div>
          </TabItem>
          <TabItem name="Hiện trạng tài sản">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageCurrentStatus && data.pic.filter(item => item.type_id === imageCurrentStatus.id).length > 0" class="container-img row mr-0 ml-0" >
                <div style="width: auto;" class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imageCurrentStatus.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh hiện trạng tài sản
              </div>
            </div>
          </TabItem>
          <TabItem name="Khác">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageJuridical && data.pic.filter(i => i.type_id === imageJuridical.id).length > 0" class="container-img row mr-0 ml-0">
                <div style="width: auto;" class="contain-img contain-img__property" v-for="(images) in data.pic.filter(i => i.type_id === imageJuridical.id)" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-else class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh khác
              </div>
            </div>
          </TabItem>
        </Tabs>
      </div>
    </div>
    <ModalMap
      v-if="openModalMap"
      @cancel="openModalMap = false"
      :location="location"
      :address="full_address_street"
      :center_map="data.coordinates"
      @action="handleCoordinates"
    />
  </div>
</template>
<style lang="scss">
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputSwitch from '@/components/Form/InputSwitch'
import InputArea from '@/components/Form/InputArea'
import ModalDeleteIndex from '@/components/Modal/ModalDeleteIndex'
import ModalMap from './modals/ModalMap'
import { Tabs, TabItem } from 'vue-material-tabs'
import File from '@/models/File'
import {LMap, LControlZoom, LTileLayer, LMarker, LTooltip, LIcon, LControl} from 'vue2-leaflet'
import Vue from 'vue'
import Icon from 'buefy'
import InputLengthArea from '@/components/Form/InputLengthArea.vue'
Vue.use(Icon)
export default {
	name: 'Step1',
	props: ['data', 'propertyTypes', 'provinces', 'districts', 'wards', 'streets', 'isEdit', 'full_address', 'projects', 'blocks', 'floors', 'apartments', 'furniture_list',
		'basic_utilities', 'directions', 'imageDescriptions', 'loai_can_ho'],
	components: {
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputArea,
		InputDatePicker,
		InputNumberNoneFormat,
		ModalMap,
		ModalDeleteIndex,
		Tabs,
		TabItem,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		InputLengthArea
	},
	computed: {
		optionsProjects () {
			return {
				data: this.projects,
				id: 'id',
				key: 'name'
			}
		},
		optionsBlocks () {
			return {
				data: this.blocks,
				id: 'id',
				key: 'name'
			}
		},
		optionsFloors () {
			return {
				data: this.floors,
				id: 'id',
				key: 'name'
			}
		},
		optionsLoaiCanHo () {
			return {
				data: this.loai_can_ho,
				id: 'id',
				key: 'description'
			}
		},
		optionsApartments () {
			return {
				data: this.apartments,
				id: 'id',
				key: 'name'
			}
		},
		optionsType () {
			return {
				data: this.propertyTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsProvince () {
			return {
				data: this.provinces,
				id: 'id',
				key: 'name'
			}
		},
		optionsDistrict () {
			return {
				data: this.districts,
				id: 'id',
				key: 'name'
			}
		},
		optionsWard () {
			return {
				data: this.wards,
				id: 'id',
				key: 'name'
			}
		},
		optionsStreet () {
			return {
				data: this.streets,
				id: 'id',
				key: 'name'
			}
		},
		optionsDistance () {
			return {
				data: this.distances,
				id: 'id',
				key: 'name'
			}
		},
		optionDirection () {
			return {
				data: this.directions,
				id: 'id',
				key: 'description'
			}
		},
		optionFurniture () {
			return {
				data: this.furniture_list,
				id: 'id',
				key: 'description'
			}
		},
		optionYearBuild () {
			return {
				data: this.built_years,
				id: 'year',
				key: 'year'
			}
		}
	},
	data () {
		return {
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			showCardDetailAppraise: true,
			showCardDetailTraffic: true,
			showCardDetailEconomicAndSocial: true,
			showCardDetailImage: true,
			showDetailPlanning: true,
			openModalMap: false,
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
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
			type: '',
			file: '',
			material: '',
			imageType: null,
			imgOverall: null,
			imageCurrentStatus: null,
			imageJuridical: null,
			built_years: []
		}
	},
	async mounted () {
		if (this.$refs.map_step1.mapObject) {
			this.$nextTick(() => {
				this.$refs.map_step1.mapObject.invalidateSize()
			})
		}
		await this.initMap()
		if (this.built_years && this.built_years.length === 0) {
			this.handleBuiltYear()
		}
		await this.getImageDescriptions(this.imageDescriptions)
		// // console.log(this.data)
	},
	methods: {
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		},
		getImageDescriptions (data) {
			this.imageType = data.find(imageDescription => imageDescription.description.toLowerCase() === 'đường tiếp giáp tài sản thẩm định giá')
			this.imgOverall = data.find(imageDescription => imageDescription.description.toLowerCase() === 'tổng thể tài sản thẩm định giá')
			this.imageCurrentStatus = data.find(imageDescription => imageDescription.description.toLowerCase() === 'hiện trạng tài sản thẩm định giá')
			this.imageJuridical = data.find(imageDescription => imageDescription.description.toLowerCase() === 'pháp lý tài sản')
		},
		handleClickUtilities (event) {
			// // console.log(this.data.apartment_asset_properties.utilities, 'event')
		},
		handleBuiltYear () {
			const year = new Date().getFullYear()
			for (let i = 1970; i <= year; i++) {
				this.built_years.push(
					{
						year: i
					}
				)
			}
			function compare (a, b) {
				if (a.year > b.year) { return -1 }
				if (a.year < b.year) { return 1 }
				return 0
			}
			return this.built_years.sort(compare)
		},
		changeUsingYear () {

		},
		handleWCNum (event) {
			this.data.apartment_asset_properties.wc_num = event
		},
		handleBedroomNum (event) {
			this.data.apartment_asset_properties.bedroom_num = event
		},
		handleArea (event) {
			this.data.apartment_asset_properties.area = event
		},
		handleChangeProject (event) {
			this.$emit('handleChangeProject', event)
		},
		handleChangeBlock (event) {
			this.$emit('handleChangeBlock', event)
		},
		handleChangeFloor (event) {
			this.$emit('handleChangeFloor', event)
		},
		handleChangeApartment (event) {

		},
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},

		async initMap () {
			// eslint-disable-next-line no-undef
			if (this.data.coordinates) {
				this.map.center = [this.data.coordinates.split(',')[0], this.data.coordinates.split(',')[1]]
				this.markerLatLng = [this.data.coordinates.split(',')[0], this.data.coordinates.split(',')[1]]
				this.map.zoom = 16
			} else {
				this.markerLatLng = [10.964112, 106.856461]
				this.map.center = [10.964112, 106.856461]
			}
		},
		changeProvince (provinceId) {
			this.$emit('getDistrict', provinceId)
		},
		changeDistrict (id) {
			this.$emit('getWardStreet', id)
		},
		changeWard (id) {
			this.$emit('getWard', id)
		},
		changeStreet (id) {
			this.$emit('changeStreet', id)
		},
		changeDistance (id) {
			this.$emit('changeDistance', id)
		},
		changeAssetType (id) {
			this.$emit('getAssetType', id)
		},
		handleChangeRoadFrontSide (value) {
			this.data.traffic_infomation.main_road_length = value
		},
		// handle coordinates from map
		handleOpenModalMap () {
			this.openModalMap = true
			this.key_map += 1
		},
		handleCoordinates (coordinates) {
			this.data.coordinates = coordinates
			this.location.lat = coordinates.split(',')[0]
			this.location.lng = coordinates.split(',')[1]
			this.map.center = [parseFloat(this.location.lat), parseFloat(this.location.lng)]
			this.markerLatLng = [parseFloat(this.location.lat), parseFloat(this.location.lng)]
		},
		async handleAddTurning () {
			await this.$emit('addTurning')
			await this.getTheLastTurningTime()
		},
		async handleDeleteTurning (index) {
			await this.$emit('deleteTurning', index)
			await this.getTheLastTurningTime()
		},
		handleChangeRoadDistance (value, index) {
			this.$emit('changeRoadDistance', value, index)
		},
		async handleChangeRoadAlley (value, index) {
			await this.$emit('changeRoadAlley', value, index)
			await this.getTheLastTurningTime()
		},
		handleView () {
			if (this.url === 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}') {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url = 'https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile'
				this.imageMap = false
			} else {
				this.url = 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
				this.imageMap = true
			}
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		titleCase (str) {
			var splitStr = str.toLowerCase().split(' ')
			for (var i = 0; i < splitStr.length; i++) {
				// You do not need to check if i is larger than splitStr length, as your for does that for you
				// Assign it back to the array
				splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1)
			}
			// Directly return the joined string
			return splitStr.join(' ')
		},
		async handleChangeAlley () {
			if (this.data.traffic_infomation.property_turning_time.length > 0) {
				await this.getTheLastTurningTime()
			}
			if (this.data.traffic_infomation.property_turning_time.length === 0) {
				await this.$emit('addTurning')
			}
		},
		async getTheLastTurningTime () {
			if (this.addressName.street) {
				const last_item = this.data.traffic_infomation.property_turning_time.length - 1
				let main_road_length = this.data.traffic_infomation.property_turning_time[last_item].main_road_length ? this.data.traffic_infomation.property_turning_time[last_item].main_road_length : 0
				let streetName = ''
				let description = ''
				streetName = this.addressName.street.toLowerCase().includes('đường') ? this.titleCase(this.addressName.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.addressName.street)}`
				await this.materials.forEach(material => {
					if (material.id === this.data.traffic_infomation.property_turning_time[last_item].material_id) {
						this.material = material.description
					}
				})
				description = 'Tiếp giáp ' + (this.material ? this.material.toLowerCase() : '') + ' rộng khoảng ' + this.formatFloat(main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
				await this.$emit('changeDescriptionFrontSide', description)
			} else {}
		},
		handleChangeFrontSide () {
			if (this.addressName.street) {
				let streetName = this.addressName.street.toLowerCase().includes('đường') ? this.titleCase(this.addressName.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.addressName.street)}`
				let description = 'Tiếp giáp mặt tiền ' + `${streetName}`
				this.$emit('changeDescriptionFrontSide', description)
			} else {
				this.$toast.open({
					message: 'Vui lòng chọn địa chỉ',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async changeMaterial () {
			await this.getTheLastTurningTime()
		},
		onImageChange (e, type) {
			const typeImage = this.imageDescriptions.find(imageDescription => imageDescription.description.toLowerCase() === type)
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
					this.type = typeImage.id
					this.createImage()
					this.uploadImage()
				} else {
					this.$toast.open({
						message: 'Hình không đúng định dạng vui lòng kiểm tra lại',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
		},
		createImage () {
			let reader = new FileReader()
			let v = this
			reader.onload = (e) => {
				v.image = e.target.result
			}
			reader.readAsDataURL(this.file)
		},
		uploadImage () {
			this.isLoading = true
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				if (response && response.data) {
					const item = {
						type_id: this.type,
						link: response.data.data.link
					}
					// this.$emit('uploadImage', item)
					this.data.pic.push(item)
					this.isLoading = false
				} else if (response.data.error) {
					this.isLoading = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		removeImage (index) {
			this.data.pic.splice(index, 1)
		}
	}
}
</script>
<style scoped lang="scss">

.div_radio {
  margin-bottom: 0.5rem;
}
.form-map {
  height: 100%;
  flex: 1;
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

.form-group-container {
  margin-top: 10px;
}

.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: end;
  background: #FFFFFF;
  border-radius: 5.88235px;
  padding: 0.5rem;
  img {
    width: 100%;
    height: auto;
		min-width: 0.75rem;
  }
}

.btn {
  &-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 100px;
    color: #fff;
    margin: 15px 0 0;
    box-sizing: border-box;

    &:hover {
      border-color: #dc8300;
    }
  }
}

.img-dropdown {
  cursor: pointer;
  width: 18px;
  &__hide {
    transform: rotate(90deg);
    transition: .3s;
  }
}

.img-locate {
  cursor: pointer;
  position: absolute;
  right: 14px;
  top: 2.1rem;
  background-color: #f5f5f5;
  height: 2.1rem;
  width: 32px;
  display: grid;
  place-items: center;

  img {
    height: 60%;
  }
}

// .text-error {
//   color: #cd201f;
//   font-size: 14pxfont-size: 14px;
// }

.select-group {
  background-color: #F6F7FB;
  border: 1px solid #E8E8E8;
  border-radius: 3px;
  padding: 16px 22px;

  .select-title {
    color: #00507C;
    font-weight: 700;
    white-space: nowrap;
    margin-bottom: unset !important;
  }
}
.img_add {
  width: 100%;
  height: 100% !important;
  cursor: pointer;
}
.container_input {
  border-radius: 10px;
  border: 2px solid #617F9E;
  width: 100%;
  height: 100%;
  position: relative;
}
.input_file_4 {
  left: 0;
  opacity: 0;
  height: 100%;
  width: 100%;
  cursor: pointer;
  position: absolute;
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
.icon_marker{
  width: 25px;
}
.content_economy {
  font-weight: 500;
  margin-left: 1.5rem;
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
}
.content_form {
  padding-left: 1.5rem;
}

.sub_header_title {
    background-color: #F6F7FB;
    border: 1px solid #E8E8E8;
    border-radius: 3px;
    padding: 0.5rem 2rem;
    position: relative;
    color: #00507C;
    font-weight: 700;
    font-size: 1.125rem;
    .label {
      margin-right: 15px;
    }
    label {
      margin: 0;
    }
    &::before {
      content: '';
      position: absolute;
      height: calc(100% - 16px);
      width: 3px;
      background-color: #99D161;
      border-radius: 3px;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
    }
  }
.delete {
    background: #617F9E;
    color: #FFFFFF;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: 700;
}
.infor-box {
  padding: 1rem;
  border-radius: 12px 15px;
  background-color: #EEF9FF;
  border: 1px solid #007EC6;
  color: #446B92;
  @media (max-height: 660px) {
    font-size: 12px;
  }
  @media (max-height: 970px) and (min-height: 660px) {
    font-size: 14px;
  }
}
.justify-content-space-evenly {
  justify-content: space-evenly !important;
}
.input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  input {
    width: 22px;
    height: 22px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #DEE6EE;
        &:after {
          display: block;
        }
      }
    }
    &:disabled {
      & ~ .check-mark {
        background-color: #DEE6EE;
      }
    }
  }
  .check-mark {
    position: absolute;
    top: 0px;
    left: 0;
    cursor: pointer;
    width: 22px;
    height: 22px;
    font-size: 18px;
    font-weight: bold;
    color: #617F9E;
    // background-color: #617F9E;
    border: 2px solid #617F9E;
    border-radius: 4px;
    &:after {
      content: "\2713";
      position: absolute;
      display: none;
      left: 50%;
      top: -3px;
      width: 5px;
      height: 10px;
      // border: solid #FFFFFF;
      // border-width: 0 3px 3px 0;
      -webkit-transform: rotate(0deg) translate(-125%, -25%);
      -ms-transform: rotate(0deg) translate(-125%, -25%);
      transform: rotate(0deg) translate(-125%, -25%);
    }
  }
}
.asset-img {
	height: 10rem;
	border: 1px var(--primary) solid;
	border-radius: 9px;
}
.contain-img__property {
	padding-left: 0px;
	padding-bottom: 5px;
}
</style>
