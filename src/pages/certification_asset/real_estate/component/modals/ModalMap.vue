<template>
	<div v-if="!isMobile()" class="modal-delete d-flex justify-content-center align-items-center">
		<div class="modal-detail d-flex justify-content-center align-items-center" v-if="isOpenLoading" style="z-index: 1032;" >
			<vep
				:progress="progress"
				color="#7579ff"
				empty-color="transparent"
				:empty-color-fill="emptyColorFill"
				:size="180"
				:thickness="5"
				:empty-thickness="3"
				lineMode="out 5"
				:legend="false"
				animation="default 0 0"
				fontSize="1.5rem"
			>
			<img slot="legend-caption" src="@/assets/images/search_for_real_estate.jpeg" style="border-radius: 100px;
    height: 160px;
    width: 160px
px
;"/>
			</vep>
		</div>
		<div class="modal-detail d-flex justify-content-center align-items-center" v-if="isOpen" >
        <div class="card" style="padding:  10px;">
          <div class="container-title" style="margin-bottom: 20px;width: 105%;
    margin-left: -10px;">
            <div class="d-lg-flex d-block shadow-bottom" style="    margin-bottom: -20px;">
              <h2 class="title">TÌM KIẾM THEO SỐ TỜ, SỐ THỬA</h2>
            </div>
          </div>
          <div class="contain-detail" style="margin-top: 0;">
            <div class="row">
				<div class="col-12">
					<InputCategory
						v-model="emCityCode"
						vid="emCityCode"
						label="Tỉnh/thành"
						class="mb-3"
						@change="changeProvince"
						:options="optionsProvince"
					/>
				</div>
				<div class="col-12">
					<InputCategory
						v-model="emDistrictCode"
						vid="emDistrictCode"
						label="Quận/huyện"
						class="mb-3"
						@change="changeDistrict"
						:options="optionsDistrict"
					/>
				</div>
				<div class="col-12">
					<InputCategory
						v-model="emWardCode"
						vid="emWardCode"
						label="Phường/xã"
						class="mb-3"
						:options="optionsWard"
					/>
				</div>
              <div class="col-6">
				<InputText
					v-model="emSoToCode"
					vid="emSoToCode"
					label="Số tờ"
					class="col-12 col-lg-12 form-group-container"
				/>
              </div>
			  <div class="col-6">
				<InputText
					v-model="emSoThuaCode"
					vid="emSoThuaCode"
					label="Số thửa"
					class="col-12 col-lg-12 form-group-container"
				/>
              </div>
            </div>
          </div>
          <div class="d-md-flex d-block justify-content-center align-items-center" style="margin-top: 30px;">
          <div class="d-md-flex d-block">
            <button  @click="closeModal" class="btn btn-white text-nowrap" >
              <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
            </button>
            <button class="btn btn-white btn-orange text-nowrap" @click="searchByToThua()" :disabled="disabledButton">
              <img src="@/assets/icons/ic_search.svg" style="margin-right: 12px" alt="save"/>Tìm kiếm
            </button>
          </div>
				</div>
        </div>
      </div>
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<div class="card" style="">
				<!-- <div class="" v-if="isOpen" @click.self="closeModal" style="top: 9vh;position: absolute; overflow: scroll;
    z-index: 999999;transform: none; transition: transform 525ms cubic-bezier(0, 0, 0.2, 1) 5ms;width: 27%;
    height: -webkit-fill-available;">
      <div class="">
        <div class="card" style="">
			<div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">TÌM KIẾM THEO SỐ TỜ, SỐ THỬA</h3>
              </div>
            </div>
		</div>
        <button class="" @click="closeModal" style="position: absolute;
    width: 50px;
    height: 50px;
    top: 7px;
    right: 6px;
    border-radius: 30px;
    background-color: white;
    border: 0px;"><img 
             src="@/assets/images/icon-btn-back.svg"
             alt="icon"></button> 
      </div>
    </div> -->
				<div
					v-if="modalGeoInfo"
					class="card"
					style="top: 70px;position: absolute; overflow: scroll;
    z-index: 999999;transform: none; transition: transform 525ms cubic-bezier(0, 0, 0.2, 1) 5ms;width: 37%;
    height: -webkit-fill-available;"
				>
					<button
						class=""
						@click="closeModalGeoInfo"
						style="position: absolute;
    right: 6px;
	top:  10px;
    border-radius: 30px;
    background-color: white;
    border: 0px;"
					>
						<img src="@/assets/images/icon-btn-back.svg" alt="icon" />
					</button>

					<div class="row" style="margin: 0;">
						<!-- <div class="col-12">
							<h2
								style="margin: 0px;
    line-height: 1.6;
    color: rgb(9, 44, 76);"
							>
								Thông tin chi tiết
							</h2>
						</div> -->
						<div v-if="dataResult.length ==  0" class="row">
							<h3>Không tìm thấy thông tin</h3>
						</div>
						<div v-else class="row" style="margin: 0;">
							<div class="col-12" style="margin-top:  15px;">
								<h3 style="color: rgb(9, 44, 76);">Vị trí thửa đất</h3>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-6">
								<span>Số tờ</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.so_to_ban_do ? dataResult.attributes.so_to_ban_do : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Số thửa</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.so_hieu_thua ? dataResult.attributes.so_hieu_thua : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Số nhà</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.so_nha ? dataResult.attributes.so_nha : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Đường</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.duong ? dataResult.attributes.duong : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Phường xã</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.phuong ? dataResult.attributes.phuong : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Quận huyện</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.quan ? dataResult.attributes.quan : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Tọa độ Google Map</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.wgs84 ? dataResult.attributes.wgs84 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Tọa độ địa chính</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.vn2000 ? dataResult.attributes.vn2000 : '-'}}</span>
							</div>
							<div class="col-12" style="margin-top:  15px;">
								<h3 style="color: rgb(9, 44, 76);">Đặc điểm thửa đất</h3>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-6">
								<span>Diện tích tổng (m2)</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.dien_tich_thua ? dataResult.attributes.dien_tich_thua : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Mục đích sử dụng đất</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.mdsdd ? dataResult.attributes.mdsdd : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Diện tích nằm trong lộ giới (m2)</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.dt_k_phu_hop ? dataResult.attributes.dt_k_phu_hop : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Loại đất quy hoạch</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.loai_dat_quy_hoach ? dataResult.attributes.loai_dat_quy_hoach : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Chiều dài</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.chieu_sau ? dataResult.attributes.chieu_sau : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Chiều rộng</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.mat_tien ? dataResult.attributes.mat_tien : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Hình dáng</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.nhan_dien ? dataResult.attributes.nhan_dien : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Hướng</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.huong ? dataResult.attributes.huong : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Số mặt tiếp giáp</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.tong_mat_tien ? dataResult.attributes.tong_mat_tien : '-'}}</span>
							</div>
							<div class="col-12" style="margin-top:  15px;">
								<h3 style="color: rgb(9, 44, 76);">Thông tin khác</h3>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-6">
								<span>Phân cấp</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.tiep_giap_1 ? dataResult.attributes.tiep_giap_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Độ rộng đường nhỏ nhất</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.rong_hem_nho_1 ? dataResult.attributes.rong_hem_nho_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Độ rộng đường trước nhà</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.hem_truoc_nha_1 ? dataResult.attributes.hem_truoc_nha_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Khoảng cách tới đường chính</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.khoang_cach_1 ? dataResult.attributes.khoang_cach_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Quyết định</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.quyet_dinh ? dataResult.attributes.quyet_dinh : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Đồ án</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.do_an ? dataResult.attributes.do_an : '-'}}</span>
							</div>
							<!-- <div class="col-12" style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"></div> -->
						</div>
					</div>
				</div>
				<div class="d-block d-sm-flex justify-content-between my-3">
					<!-- <InputSwitchToThua
                                v-model="search_to_thua"
                                vid="search_to_thua"
                                @input="changeSearchToThua"
                              /> -->
					<div class="search-container w-100 d-flex">
						<gmap-autocomplete
							:value="search_address"
							:placeholder="strPlaceHolder"
							@place_changed="setPlace"
							@change="changePlace"
							@keyup.enter="changePlace"
							class="input-map"
							:options="{
								fields: ['geometry', 'address_components', 'formatted_address'],
								componentRestrictions: { country: 'vn' }
							}"
						/>
						<div class="icon-container" @click="handleSearch">
							<img src="@/assets/icons/ic_search.svg" alt="" />
						</div>
					</div>
					<button class="btn btn-search" title="Tìm kiếm theo tờ, thửa" alt="Tìm kiếm theo tờ, thửa" type="button" @click="handleOpenEM" style="background-color: #FFFFFF;padding: 0;pointer-events: auto">
				<!-- <svg width="25" height="25" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
                    fill="#007EC6"/>
                </svg> -->
				<img src="https://firebasestorage.googleapis.com/v0/b/fast-value.appspot.com/o/assets%2Fland%20parcel.2.png?alt=media&token=3a1a0bd8-4c64-4dbe-9373-423e664f3669" style="height: -webkit-fill-available;
    width: 75px;">
			</button>
					<input type="text" id="coordinate" :value="address" class="d-none" />
					<button class="btn btn-search" type="button" @click="handleAction">
						Xác nhận
					</button>
					<button
						class="btn btn-white btn-cancel"
						type="button"
						@click="handleCancel"
					>
						Trở lại
					</button>
				</div>
				<div class="main-map">
					<div id="mapid" class="layer-map">
						<l-map
							ref="lmap"
							style="height: 100%;"
							:zoom="map.zoom"
							:center="map.center"
							:maxZoom="20"
							:options="{ zoomControl: false }"
							@click="choosePoint($event)"
						>
							<l-tile-layer
								:url="url"
								:attribution="attribution"
								:options="{ maxNativeZoom: 20, maxZoom: 20 }"
							></l-tile-layer>
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
							<!-- <l-tile-layer
			:url="url_quyhoach"
			:min-zoom="12"
			:options="{ maxNativeZoom: 19, maxZoom: 20}" -->
							/>
							<!-- <l-tile-layer
                          url="https://cdn.estatemanner.com/tile/paper_map/thanh_pho_ha_noi/quan_cau_giay/{z}/{x}/{y}.png"
                          :min-zoom="12"
                          :options="{ maxNativeZoom: 19, maxZoom: 20}"
                        /> -->
							<l-control-zoom position="topright"></l-control-zoom>
							<l-control position="topright">
								<button
									class="btn btn-orange mini_btn"
									type="button"
									@click="geoLocate"
								>
									<img
										src="@/assets/icons/ic_locate_white.svg"
										alt="location"
									/>
								</button>
							</l-control>
							<l-control position="bottomright">
								<button class="btn btn-map" @click="handleView" type="button">
									<img
										v-if="!imageMap"
										src="@/assets/images/im_map.png"
										alt=""
									/>
									<img
										v-if="imageMap"
										src="@/assets/images/im_satellite.png"
										alt=""
									/>
								</button>
							</l-control>
							<l-control-layers position="bottomright"></l-control-layers>
							<l-marker :lat-lng="markerLatLng">
								<l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
									<img
										style="width: 60px !important"
										class="icon_marker"
										src="@/assets/images/svg_home.svg"
										alt=""
									/>
								</l-icon>
								<l-tooltip>Vị trí tài sản</l-tooltip>
							</l-marker>
							<l-geo-json :geojson="geo_data"></l-geo-json>
							<l-control position="topleft">
								<button v-if="dataResult"
									class="btn btn-orange mini_btn"
									type="button"
									@click="geoInfo"
									style="    border-radius: 50px;
    background: white;"
								>
									<svg
										width="40"
										height="40"
										viewBox="0 0 12 13"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
											fill="#007EC6"
										/>
									</svg>
								</button>
							</l-control>
						</l-map>
					</div>
				</div>
			</div>
		</ValidationObserver>
	</div>
	<div v-else class="modal-delete" style="padding:0;">
		<div class="modal-detail d-flex justify-content-center align-items-center" v-if="isOpenLoading" style="z-index: 1032;" >
			<vep
				:progress="progress"
				color="#7579ff"
				empty-color="transparent"
				:empty-color-fill="emptyColorFill"
				:size="180"
				:thickness="5"
				:empty-thickness="3"
				lineMode="out 5"
				:legend="false"
				animation="default 0 0"
				fontSize="1.5rem"
			>
			<img slot="legend-caption" src="@/assets/images/search_for_real_estate.jpeg" style="border-radius: 100px;
    height: 160px;
    width: 160px
px
;"/>
			</vep>
		</div>
		<div class="modal-detail d-flex justify-content-center align-items-center" v-if="isOpen" >
        <div class="card" style="padding:  10px;">
          <div class="container-title" style="margin-bottom: 20px;text-align: center;">
            <div class="d-lg-flex d-block shadow-bottom" style="    margin-bottom: -20px;">
              <h2 class="title">TÌM KIẾM THEO SỐ TỜ, SỐ THỬA</h2>
            </div>
          </div>
          <div class="contain-detail" style="margin-top: 0;">
            <div class="row">
				<div class="col-12">
					<InputCategory
						v-model="emCityCode"
						vid="emCityCode"
						label="Tỉnh/thành"
						class="mb-3"
						@change="changeProvince"
						:options="optionsProvince"
					/>
				</div>
				<div class="col-12">
					<InputCategory
						v-model="emDistrictCode"
						vid="emDistrictCode"
						label="Quận/huyện"
						class="mb-3"
						@change="changeDistrict"
						:options="optionsDistrict"
					/>
				</div>
				<div class="col-12">
					<InputCategory
						v-model="emWardCode"
						vid="emWardCode"
						label="Phường/xã"
						class="mb-3"
						:options="optionsWard"
					/>
				</div>
              <div class="col-6">
				<InputText
					v-model="emSoToCode"
					vid="emSoToCode"
					label="Số tờ"
					class="col-12 col-lg-12 form-group-container"
				/>
              </div>
			  <div class="col-6">
				<InputText
					v-model="emSoThuaCode"
					vid="emSoThuaCode"
					label="Số thửa"
					class="col-12 col-lg-12 form-group-container"
				/>
              </div>
            </div>
          </div>
          <!-- <div class="d-md-flex d-block justify-content-center align-items-center" style="margin-top: 30px;">
          <div class="d-md-flex d-block">
            <button  @click="closeModal" class="btn btn-white text-nowrap" >
              <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
            </button>
            <button class="btn btn-white btn-orange text-nowrap" @click="searchByToThua()" :disabled="disabledButton">
              <img src="@/assets/icons/ic_search.svg" style="margin-right: 12px" alt="save"/>Tìm kiếm
            </button>
          </div>
				</div> -->
			<div class="d-md-flex d-block justify-content-center align-items-center" style="margin-top: 30px;">
					<div class="d-lg-flex d-block button-contain row" style="justify-content: space-around;display: flex!important;">
						<button @click="closeModal" class="btn btn-white text-nowrap col-6" style="width: unset;margin: 0;padding: 0;">
							<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
						</button>
						<button :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap col-6" @click="searchByToThua()" style="width: unset;margin: 0;padding: 0;">
							<img src="@/assets/icons/ic_search.svg" style="margin-right: 12px" alt="save"/>Tìm kiếm
						</button>
					</div>
				</div>
        </div>
      </div>
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<div class="card" style="">
				<!-- <div class="" v-if="isOpen" @click.self="closeModal" style="top: 9vh;position: absolute; overflow: scroll;
    z-index: 999999;transform: none; transition: transform 525ms cubic-bezier(0, 0, 0.2, 1) 5ms;width: 27%;
    height: -webkit-fill-available;">
      <div class="">
        <div class="card" style="">
			<div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">TÌM KIẾM THEO SỐ TỜ, SỐ THỬA</h3>
              </div>
            </div>
		</div>
        <button class="" @click="closeModal" style="position: absolute;
    width: 50px;
    height: 50px;
    top: 7px;
    right: 6px;
    border-radius: 30px;
    background-color: white;
    border: 0px;"><img 
             src="@/assets/images/icon-btn-back.svg"
             alt="icon"></button> 
      </div>
    </div> -->
				<div
					v-if="modalGeoInfo"
					class="card"
					style="position: fixed; overflow: scroll;
    z-index: 999999;transform: none; transition: transform 525ms cubic-bezier(0, 0, 0.2, 1) 5ms;width: 100%;
    height: -webkit-fill-available;
    margin-bottom: 60px;left:0;"
				>
					<button
						class=""
						@click="closeModalGeoInfo"
						style="position: absolute;
    right: 6px;
	top:  10px;
    border-radius: 30px;
    background-color: white;
    border: 0px;"
					>
						<img src="@/assets/images/icon-btn-back.svg" alt="icon" />
					</button>

					<div class="row" style="margin: 0;">
						<!-- <div class="col-12">
							<h2
								style="margin: 0px;
    line-height: 1.6;
    color: rgb(9, 44, 76);"
							>
								Thông tin chi tiết
							</h2>
						</div> -->
						<div v-if="dataResult.length ==  0" class="row">
							<h3>Không tìm thấy thông tin</h3>
						</div>
						<div v-else class="row" style="margin: 0;">
							<div class="col-12" style="margin-top:  15px;">
								<h3 style="color: rgb(9, 44, 76);">Vị trí thửa đất</h3>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-6">
								<span>Số tờ</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.so_to_ban_do ? dataResult.attributes.so_to_ban_do : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Số thửa</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.so_hieu_thua ? dataResult.attributes.so_hieu_thua : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Số nhà</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.so_nha ? dataResult.attributes.so_nha : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Đường</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.duong ? dataResult.attributes.duong : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Phường xã</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.phuong ? dataResult.attributes.phuong : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Quận huyện</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.quan ? dataResult.attributes.quan : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Tọa độ Google Map</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.wgs84 ? dataResult.attributes.wgs84 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Tọa độ địa chính</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.vn2000 ? dataResult.attributes.vn2000 : '-'}}</span>
							</div>
							<div class="col-12" style="margin-top:  15px;">
								<h3 style="color: rgb(9, 44, 76);">Đặc điểm thửa đất</h3>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-6">
								<span>Diện tích tổng (m2)</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.dien_tich_thua ? dataResult.attributes.dien_tich_thua : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Mục đích sử dụng đất</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.mdsdd ? dataResult.attributes.mdsdd : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Diện tích nằm trong lộ giới (m2)</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.dt_k_phu_hop ? dataResult.attributes.dt_k_phu_hop : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Loại đất quy hoạch</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.loai_dat_quy_hoach ? dataResult.attributes.loai_dat_quy_hoach : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Chiều dài</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.chieu_sau ? dataResult.attributes.chieu_sau : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Chiều rộng</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.mat_tien ? dataResult.attributes.mat_tien : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Hình dáng</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.nhan_dien ? dataResult.attributes.nhan_dien : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Hướng</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.huong ? dataResult.attributes.huong : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Số mặt tiếp giáp</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.tong_mat_tien ? dataResult.attributes.tong_mat_tien : '-'}}</span>
							</div>
							<div class="col-12" style="margin-top:  15px;">
								<h3 style="color: rgb(9, 44, 76);">Thông tin khác</h3>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-6">
								<span>Phân cấp</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.tiep_giap_1 ? dataResult.attributes.tiep_giap_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Độ rộng đường nhỏ nhất</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.rong_hem_nho_1 ? dataResult.attributes.rong_hem_nho_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Độ rộng đường trước nhà</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.hem_truoc_nha_1 ? dataResult.attributes.hem_truoc_nha_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Khoảng cách tới đường chính</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.khoang_cach_1 ? dataResult.attributes.khoang_cach_1 : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Quyết định</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.quyet_dinh ? dataResult.attributes.quyet_dinh : '-'}}</span>
							</div>
							<div class="col-6">
								<span>Đồ án</span>
							</div>
							<div class="col-6">
								<span>{{ dataResult.attributes.do_an ? dataResult.attributes.do_an : '-'}}</span>
							</div>
							<!-- <div class="col-12" style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"></div> -->
						</div>
					</div>
				</div>
				<div class="my-3 row">
					<!-- <InputSwitchToThua
                                v-model="search_to_thua"
                                vid="search_to_thua"
                                @input="changeSearchToThua"
                              /> -->
					<div class="search-container d-flex col-10" style="margin-right: 0;">
						<gmap-autocomplete
							:value="search_address"
							:placeholder="strPlaceHolder"
							@place_changed="setPlace"
							@change="changePlace"
							@keyup.enter="changePlace"
							class="input-map"
							:options="{
								fields: ['geometry', 'address_components', 'formatted_address'],
								componentRestrictions: { country: 'vn' }
							}"
						/>
						<div class="icon-container" @click="handleSearch">
							<img src="@/assets/icons/ic_search.svg" alt="" />
						</div>
					</div>
					<div class="col-2" style="padding-left:0;">
						<button class="btn btn-search" title="Tìm kiếm theo tờ, thửa" alt="Tìm kiếm theo tờ, thửa" type="button" @click="handleOpenEM" style="background-color: #FFFFFF;padding: 0;pointer-events: auto;    margin-top: 0;">
				<img src="https://firebasestorage.googleapis.com/v0/b/fast-value.appspot.com/o/assets%2Fland%20parcel.2.png?alt=media&token=3a1a0bd8-4c64-4dbe-9373-423e664f3669" style="height: -webkit-fill-available;
    width: 75px;">
			</button>
			</div>
					<input type="text" id="coordinate" :value="address" class="d-none" />
					<div class="row" style="margin:0; justify-content: space-between;">
						<div class="col-6">
							<button class="btn btn-search" type="button" @click="handleAction">
								Xác nhận
							</button>
						</div>
						<div class="col-6">
							<button
								class="btn btn-white btn-cancel"
								type="button"
								@click="handleCancel"
							>
								Trở lại
							</button>
						</div>
					</div>
				</div>
				<div class="main-map" style="height: -webkit-fill-available;margin-bottom: 60px;">
					<div id="mapid" class="layer-map">
						<l-map
							ref="lmap"
							style="height: 100%;"
							:zoom="map.zoom"
							:center="map.center"
							:maxZoom="20"
							:options="{ zoomControl: false }"
							@click="choosePoint($event)"
						>
							<l-tile-layer
								:url="url"
								:attribution="attribution"
								:options="{ maxNativeZoom: 20, maxZoom: 20 }"
							></l-tile-layer>
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
							<!-- <l-tile-layer
			:url="url_quyhoach"
			:min-zoom="12"
			:options="{ maxNativeZoom: 19, maxZoom: 20}" -->
							/>
							<!-- <l-tile-layer
                          url="https://cdn.estatemanner.com/tile/paper_map/thanh_pho_ha_noi/quan_cau_giay/{z}/{x}/{y}.png"
                          :min-zoom="12"
                          :options="{ maxNativeZoom: 19, maxZoom: 20}"
                        /> -->
							<l-control-zoom position="topright"></l-control-zoom>
							<l-control position="topright">
								<button
									class="btn btn-orange mini_btn"
									type="button"
									@click="geoLocate"
								>
									<img
										src="@/assets/icons/ic_locate_white.svg"
										alt="location"
									/>
								</button>
							</l-control>
							<l-control position="bottomright">
								<button class="btn btn-map" @click="handleView" type="button">
									<img
										v-if="!imageMap"
										src="@/assets/images/im_map.png"
										alt=""
									/>
									<img
										v-if="imageMap"
										src="@/assets/images/im_satellite.png"
										alt=""
									/>
								</button>
							</l-control>
							<l-control-layers position="bottomright"></l-control-layers>
							<l-marker :lat-lng="markerLatLng">
								<l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
									<img
										style="width: 60px !important"
										class="icon_marker"
										src="@/assets/images/svg_home.svg"
										alt=""
									/>
								</l-icon>
								<l-tooltip>Vị trí tài sản</l-tooltip>
							</l-marker>
							<l-geo-json :geojson="geo_data"></l-geo-json>
							<l-control position="topleft">
								<button v-if="dataResult"
									class="btn btn-orange mini_btn"
									type="button"
									@click="geoInfo"
									style="    border-radius: 50px;
    background: white;"
								>
									<svg
										width="40"
										height="40"
										viewBox="0 0 12 13"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
											fill="#007EC6"
										/>
									</svg>
								</button>
							</l-control>
						</l-map>
					</div>
				</div>
			</div>
		</ValidationObserver>
	</div>
</template>
<style lang="scss">
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import {
	LMap,
	LControlZoom,
	LTileLayer,
	LMarker,
	LTooltip,
	LIcon,
	LControl,
	LControlLayers,
	LGeoJson
} from "vue2-leaflet";
import Vue from "vue";
import Icon from "buefy";
import InputText from "@/components/Form/InputText";
import axios from '@/plugins/axios'
import File from '@/models/File'
import InputSwitchToThua from '@/components/Form/InputSwitchToThua'
import cityJson from '@/assets/json/phuluc_dmhc/city/city.json'
import InputCategory from '@/components/Form/InputCategory'


Vue.use(Icon);
export default {
	name: "ModalMapAsset",
	props: ["location", "address", "center_map"],
	components: {
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		InputText,
		LControlLayers,
		LGeoJson,
		InputSwitchToThua,
		InputCategory
	},
	data() {
		return {
			progress: 0,
			emptyColorFill: {
			radial: false,
			colors: [
				{
				color: "#754fc1",
				offset: "0",
				opacity: "0.3",
				},
				{
				color: "#366bfc",
				offset: "100",
				opacity: "0.3",
				},
			],
			},
			search_to_thua: false,
			dataResult: [],
			url_modal: "https://app.estatemanner.com/map",
			isOpen: false,
			url_quyhoach: "",
			tileProviders: [
				{
					name: "Bản đồ ranh tờ, thửa",
					visible: true,
					url: "https://cdn.estatemanner.com/tile/ranh_thua/{z}/{x}/{y}.png",
					attribution: "© Fastvalue",
					type: "overlay"
				},
				{
					name: "Bản đồ thông tin quy hoạch",
					visible: false,
					attribution: "© Fastvalue",
					url: "https://cdn.estatemanner.com/tile/qhsdd/{z}/{x}/{y}.png",
					type: "overlay"
				},
        {
					name: "Bản đồ quy hoạch lộ giới",
					visible: false,
					attribution: "© Fastvalue",
					url: "https://cdn.estatemanner.com/tile/qhlg/{z}/{x}/{y}.png",
					type: "overlay"
				}
			],
			search_address: "",
			url:
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff",
			attribution: "",
			place: "",
			provinces: [],
			places: [],
			currentPlace: null,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			imageMap: true,
			render_map: 3232102,
			strPlaceHolder: "Nhập địa điểm, vị trí hoặc tọa độ",
			geo_data: null,
			modalGeoInfo: false,
			duong:'',
			phuongxa:'',
			quanhuyen:'',
			tinhthanh:'',
			phuongxa_code:'',
			quanhuyen_code:'',
			tinhthanh_code:'',
			listCity: [],
			listDistrict: [],
			listWard: [],
			emCityCode: '',
			emDistrictCode: '',
			emWardCode: '',
			emSoToCode: '',
			emSoThuaCode: '',
			isOpenLoading: false,
			runProgress: true
		};
	},
	computed: {
		disabledButton () {
			console.log('vô vô')
			if ((this.emSoToCode || this.emSoThuaCode) && (this.emCityCode && this.emDistrictCode && this.emWardCode)) {
				return false
			} else return true
		},
		optionsProvince () {
			console.log('list tỉnh', {
				data: this.listCity,
				id: 'code',
				key: 'name_with_type'
			})
			return {
				data: this.listCity,
				id: 'code',
				key: 'name_with_type'
			}
		},
		optionsDistrict () {
			console.log('list huyện', {
				data: this.listDistrict,
				id: 'code',
				key: 'name_with_type'
			})
			return {
				data: this.listDistrict,
				id: 'code',
				key: 'name_with_type'
			}
		},
		optionsWard () {
			console.log('list xã', {
				data: this.listWard,
				id: 'code',
				key: 'name_with_type'
			})
			return {
				data: this.listWard,
				id: 'code',
				key: 'name_with_type'
			}
		},
	},
	async mounted() {
		this.search_address = this.address;
		await this.$gmapApiPromiseLazy();
		if (this.center_map !== "" && this.center_map) {
			this.map.center = [
				this.center_map.split(",")[0],
				this.center_map.split(",")[1]
			];
			this.markerLatLng = [
				this.center_map.split(",")[0],
				this.center_map.split(",")[1]
			];
		} else {
			await this.initMap(this.address, "address");
		}
		// console.log('cityJSON', cityJson)
		var result = [];

		for(var i in cityJson)
			result.push(cityJson [i])
		this.listCity = result
		console.log('city array', this.listCity)
	},
	methods: {
		changeProvince (code) {
			this.listDistrict =  []
			this.emDistrictCode = ''
			this.listWard = []
			this.emWardCode = ''
			const districtJson = require('@/assets/json/phuluc_dmhc/district/'+code+'.json')
			for(var i in districtJson)
				this.listDistrict.push(districtJson [i])
			console.log('list huyện', this.listDistrict)
			console.log('tỉnh chọn', this.emCityCode)
		},
		changeDistrict (code) {
			this.listWard = []
			this.emWardCode = ''
			const wardJson = require('@/assets/json/phuluc_dmhc/ward/'+code+'.json')
			for(var i in wardJson)
				this.listWard.push(wardJson [i])
			console.log('list xã', this.listWard)
		},
		getEmCode() {
			if (this.tinhthanh){
				if (this.listCity.length > 0) {
					let checkcotinh = 0
					for (let i = 0; i < this.listCity.length; i++) {
						let e = this.listCity[i]
						if (e.name_with_type.toLowerCase()  == this.tinhthanh.toLowerCase() || e.name.toLowerCase() == this.tinhthanh.toLowerCase()) {
							console.log('dính chưởng tỉnh thành', e)
							this.emCityCode = e.code
							if (this.quanhuyen) {
								const districtJson = require('@/assets/json/phuluc_dmhc/district/'+this.emCityCode+'.json')
								// console.log('list district', listDistrict)
								if (districtJson) {
									for(var i in districtJson)
										this.listDistrict.push(districtJson [i])
									
									if (this.listDistrict.length > 0) {
										let checkcohuyen = 0
										for (let i = 0; i < this.listDistrict.length; i++) {
											let q = this.listDistrict[i]
											if (q.name_with_type.toLowerCase()  == this.quanhuyen.toLowerCase() || q.name.toLowerCase() == this.quanhuyen.toLowerCase()) {
												console.log('dính chưởng quận huyện', q)
												this.emDistrictCode = q.code
												if (this.phuongxa) {
													console.log('vô phường xã')
													let split1 = this.phuongxa.split(' ')
													console.log('cắt', split1)
													if (split1[1] == '1' || split1[1] == '2' || split1[1] == '3' || split1[1] == '4' || split1[1] == '5' || split1[1] == '6' || split1[1] == '7' || split1[1] == '8' || split1[1] == '9') {
														this.phuongxa = split1[0]+' 0'+split1[1]
													}
													const wardJson = require('@/assets/json/phuluc_dmhc/ward/'+this.emDistrictCode+'.json')
													if (wardJson) {
														for(var i in wardJson)
															this.listWard.push(wardJson [i])

														if (this.listWard.length > 0) {
															let checkcoxa = 0
															for (let i = 0; i < this.listWard.length; i++) {
																let x = this.listWard[i]
																console.log('x',this.phuongxa)
																if (x.name_with_type.toLowerCase()  == this.phuongxa.toLowerCase() || x.name.toLowerCase() == this.phuongxa.toLowerCase()) {
																	console.log('dính chưởng phường xã', x)
																	this.emWardCode = x.code
																	return
																}
															}
															if (checkcoxa == 0) {
																return
															}
														}
													}
												}
											}
										}
										if (checkcohuyen == 0) {
											return
										}
									}
								}
							}
						}
					}
					if (checkcotinh == 0) {
						return
					}
				}
			} else {
				if (this.quanhuyen_code || this.phuongxa_code) {

				}
			}
		},
		inscreaseProgress(progress){
			console.log('gọi tăng')
			progress = progress + 1
			this.progress = progress
			if (this.progress < 100 && this.runProgress == true) {
				let that = this
				setTimeout( function() {
					that.inscreaseProgress(progress)
				}, 30)
			}
		},
		async searchByToThua(){
			this.modalGeoInfo = false
			this.progress = 0
			this.runProgress = true
			this.isOpenLoading = true
			this.inscreaseProgress(this.progress)
			await this.getEmCode()
			if ((this.emSoToCode || this.emSoThuaCode) && (this.emCityCode && this.emDistrictCode && this.emWardCode)) {
				console.log('đầy đủ thông tin')
				const APItoken = await this.getToken();
				if (APItoken){
					let formdata = {
					token: APItoken,
					land_plot: this.emSoThuaCode,
					land_sheet: this.emSoToCode,
					city_code: this.emCityCode,
					district_code: this.emDistrictCode,
					ward_code: this.emWardCode
					}
					await File.getInfoByLand({ data: formdata }).then((response) => {
					console.log('response result',response)
					let datafinal = response.data.data
					//   let that = this
					console.log('data final', datafinal)
					if (datafinal.message != 'Hệ thống đang có lỗi xảy ra, vui lòng thử lại sau' && datafinal.message != 'Không tìm thấy thông tin quy hoạch') {
						this.dataResult = datafinal.data
						this.geo_data = this.dataResult  ? this.dataResult.geo_data :  []
						this.modalGeoInfo = true
						this.isOpen  = false
						this.map.center = [
							this.geo_data.geometry.coordinates[0][0][0][1] ? this.geo_data.geometry.coordinates[0][0][0][1] : this.geo_data.geometry.coordinates[0][0][1],
							this.geo_data.geometry.coordinates[0][0][0][0] ? this.geo_data.geometry.coordinates[0][0][0][0] : this.geo_data.geometry.coordinates[0][0][0],
						]
						this.markerLatLng = [
							this.geo_data.geometry.coordinates[0][0][0][1] ? this.geo_data.geometry.coordinates[0][0][0][1] : this.geo_data.geometry.coordinates[0][0][1],
							this.geo_data.geometry.coordinates[0][0][0][0] ? this.geo_data.geometry.coordinates[0][0][0][0] : this.geo_data.geometry.coordinates[0][0][0],
						]
						this.isOpenLoading = false
						this.progress = 0
						this.runProgress = false
						// console.log('quan huyen', this.geo_data.geometry.coordinates[0][0])
					} else {
						this.$toast.open({
						message: 'Không tìm thấy dữ liệu quy hoạch',
						type: 'error',
						position: 'top-right',
						duration: 3000
						})
						this.dataResult = []
						this.geo_data = []
						this.modalGeoInfo = false
						this.isOpen  = false
						this.isOpenLoading = false
						this.progress = 0
						this.runProgress = false
					}
					})
				}
			}
		},
		// changeSearchToThua(event){
		// 	console.log('event', this.serch_to_thua)
		// 	// this.search_to_thua = true
		// },
    async getToken () {
      // const uninterceptedAxiosInstance = axios.create();
      //   const body = {
      //   client_id: 'BWflWM57LHSivze237MRNsOQxb23DUQ6',
      //   client_secret: 'K9I1955xyA_uQsiei0ucoXAUyO0rnXGz_Cvxx40ZqUOtvcEP0hZaz4pHGSHYIwql'
      //   }; // request JSON body
      //   const headers = { 'Content-type': 'application/json','Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Credentials':true }; // auth header with bearer token
      // const response = await uninterceptedAxiosInstance.post('https://app.estatemanner.com/api/v1/auth/credentials', body,{headers}).catch(function (error) {
      //     if (error.response) {
      //       console.log(error.response.data);
      //       console.log(error.response.status);
      //       console.log(error.response.headers);
      //     }
      //   })
      // console.log('data token', response.data.data.access_token)
      // if (response.data.data.access_token) {
      //   return response.data.data.access_token
      // } else {
      //   return null
      // }
      // let formData = {
      //   client_id: 'BWflWM57LHSivze237MRNsOQxb23DUQ6',
      //   client_secret: 'K9I1955xyA_uQsiei0ucoXAUyO0rnXGz_Cvxx40ZqUOtvcEP0hZaz4pHGSHYIwql'
      // }
      let token = ''
      await File.getToken().then((response) => {
				console.log('response token',response)
        token = response.data.data.data.access_token
			})
      return token
    },
    async getInfoByCoord (coordinates) {
		this.modalGeoInfo = false
		this.progress = 0
		this.runProgress  = true
		this.isOpenLoading = true
		this.inscreaseProgress(this.progress)
      const APItoken = await this.getToken();
      console.log('api token', APItoken)
      if (APItoken){
        // const uninterceptedAxiosInstance = axios.create();
        // const body = { lat: coordinates[0], lng: coordinates[1] }; // request JSON body
        // const headers = { 'Content-type': 'application/json','Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Credentials':true, 'Authorization': `Bearer ${APItoken}` }; // auth header with bearer token
        // uninterceptedAxiosInstance.post('https://app.estatemanner.com/api/v1/map/feature/coord', body, { headers })
        //     .then(response => 
        //     {
        //       console.log('response', response.data.data)
        //       this.dataResult = response.data.data
        //       this.geo_data = this.dataResult.geo_data
        //       this.modalGeoInfo = true
        //     })
        //     .catch(function (error) {
        //       that = this
        //       if (error.response) {
        //         console.log(error.response.data);
        //         console.log(error.response.status);
        //         console.log(error.response.headers);
        //         that.$toast.open({
        //             message: error.response.data.message,
        //             type: 'error',
        //             position: 'top-right',
        //             duration: 3000
        //           })
        //           that.dataResult = null
        //           that.geo_data = null
        //       }
        //     });
        let formdata = {
          token: APItoken,
          lat: coordinates[0],
          lng: coordinates[1]
        }
        await File.getInfoByCoord({ data: formdata }).then((response) => {
          console.log('response result',response)
          let datafinal = response.data.data
        //   let that = this
          console.log('data final', datafinal)
          if (datafinal.message != 'Hệ thống đang có lỗi xảy ra, vui lòng thử lại sau' && datafinal.message != 'Không tìm thấy thông tin quy hoạch') {
            this.dataResult = datafinal.data
            this.geo_data = this.dataResult  ? this.dataResult.geo_data :  []
            this.modalGeoInfo = true
			this.phuongxa_code = this.dataResult.attributes.level_3
			this.quanhuyen_code = this.dataResult.attributes.level_2
			// console.log('quan huyen', this.quanhuyen)
			this.isOpenLoading = false
			this.progress = 0
			this.runProgress = false
          } else {
            this.$toast.open({
              message: 'Không tìm thấy dữ liệu quy hoạch',
              type: 'error',
              position: 'top-right',
              duration: 3000
            })
            this.dataResult = []
            this.geo_data = []
            this.modalGeoInfo = false
			this.isOpenLoading = false
			this.progress = 0
			this.runProgress = false
          }
        })
      }
		},
		geoInfo() {
			this.modalGeoInfo = !this.modalGeoInfo;
			console.log("open", this.modalGeoInfo);
		},
		closeModalGeoInfo() {
			this.modalGeoInfo = false;
		},
		async handleOpenEM() {
			this.modalGeoInfo = false
			console.log("mở");
			await this.getEmCode()
			this.isOpen = true;
		},
		closeModal() {
			this.isOpen = false;
		},
		changePlace(event) {
			this.search_address = event.target.value;
		},
		handleView() {
			if (
				this.url ===
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff"
			) {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				// this.attribution = 'Tiles © ; Esri: Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
				this.url =
					"https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.imageMap = false;
			} else {
				this.url =
					"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.attribution = "© Fastvalue";
				this.imageMap = true;
			}
		},
		async initMap(address, type) {
			console.log('vô 3')
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder();
			await this.geocodeAddress(geocoder, address, type);
		},
		async geocodeAddress(geocoder, address, type) {
			console.log('vô 4')
			let center = {};
			let duong = ''
			let phuongxa = ''
			let quanhuyen = ''
			let tinhthanh = ''
			// let address = this.address
			if (!address) {
				address = document.getElementById("coordinate").value;
			}
			if (address) {
				let keySearch = {};
				if (type === "address") {
					keySearch = {
						address: address
					};
				} else if (type === "location") {
					keySearch = {
						location: address
					};
				}
				await geocoder.geocode(keySearch, function(results, status) {
					if (status === "OK") {
						console.log('ket qua',results[0])
						const marker = {
							position: results[0].geometry.location
						};
						center = [
							parseFloat(marker.position.lat()),
							parseFloat(marker.position.lng())
						];
						if (results[0].address_components[results[0].address_components.length - 1].long_name.toLowerCase() == 'việt nam' ){
							if (results[0].address_components.length == 5) {
								duong = results[0].address_components[0].long_name
								phuongxa = results[0].address_components[1].long_name
								quanhuyen = results[0].address_components[2].long_name
								tinhthanh = results[0].address_components[3].long_name
							} else if (results[0].address_components.length == 4) {
								phuongxa = results[0].address_components[0].long_name
								quanhuyen = results[0].address_components[1].long_name
								tinhthanh = results[0].address_components[2].long_name
							} else if (results[0].address_components.length == 3) {
								quanhuyen = results[0].address_components[0].long_name
								tinhthanh = results[0].address_components[1].long_name
							} else if (results[0].address_components.length == 2){
								tinhthanh = results[0].address_components[0].long_name
							}
						} else {
							if (results[0].address_components.length == 4) {
								duong = results[0].address_components[0].long_name
								phuongxa = results[0].address_components[1].long_name
								quanhuyen = results[0].address_components[2].long_name
								tinhthanh = results[0].address_components[3].long_name
							} else if (results[0].address_components.length == 3) {
							phuongxa = results[0].address_components[0].long_name
							quanhuyen = results[0].address_components[1].long_name
							tinhthanh = results[0].address_components[2].long_name
							} else if (results[0].address_components.length == 2) {
								quanhuyen = results[0].address_components[0].long_name
								tinhthanh = results[0].address_components[1].long_name
							} else {
								tinhthanh = results[0].address_components[0].long_name
							}
						}
						
						 
						console.log('ketqua dia chi',results[0].address_components)
						console.log('ketqua phuong xa',phuongxa)
						console.log('ketqua quan huyen',quanhuyen)
						console.log('ketqua tinh thanh',tinhthanh)
					} else {
						// alert('Geocode was not successful for the following reason: ' + status)
					}
				});
				this.map.center = center;
				this.markerLatLng = center;
				this.duong = duong;
				this.phuongxa = phuongxa;
				this.quanhuyen = quanhuyen;
				this.tinhthanh = tinhthanh;
				console.log('quan huyen', this.quanhuyen)
        console.log('gọi vô')
        // this.getInfoByCoord(this.markerLatLng)
			}
		},
		choosePoint(event) {
			// console.log('choosePoint', event)
			this.map.center = [event.latlng.lat, event.latlng.lng];
			this.markerLatLng = [event.latlng.lat, event.latlng.lng];
			this.search_address = event.latlng.lat + "," + event.latlng.lng;

			// this.geo_data = this.dataResult.geo_data;
      this.getInfoByCoord(this.markerLatLng);
			// let latlng = {
			//   lat: event.latlng.lat,
			//   lng: event.latlng.lng
			// }
			// this.initMap(latlng, 'location')
		},
		setPlace(place) {
			if (place.geometry && place.geometry.location) {
				console.log('vô I')
				this.search_address = place.formatted_address;
				this.currentPlace = place;
				this.map.center = [
					place.geometry.location.lat(),
					place.geometry.location.lng()
				];
				this.markerLatLng = [
					place.geometry.location.lat(),
					place.geometry.location.lng()
				];
				this.initMap(this.search_address, "address");
        this.getInfoByCoord(this.markerLatLng)
			} else {
				console.log('vô II')
				if (place.name) {
					console.log('vô II.1')
					let location = place.name;
					this.search_address = place.name;
					if (
						location.split(",") &&
						location.split(",").length === 2 &&
						parseFloat(location.split(",")[0]) &&
						parseFloat(location.split(",")[1])
					) {
						console.log('vô II.1.1')
						let lat = parseFloat(location.split(",")[0]);
						let lng = parseFloat(location.split(",")[1]);
						this.map.center = [lat, lng];
						this.markerLatLng = [lat, lng];
            this.getInfoByCoord(this.markerLatLng)
					} else {
						console.log('vô II.1.2')
						this.initMap(location, "address");
					}
				} else {
					console.log('vô II.2')
					this.initMap(this.search_address, "address");
				}
			}
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		handleAction(event) {
			const data = this.markerLatLng[0] + "," + this.markerLatLng[1];
			this.$emit("cancel", event);
			this.$emit("action", data);
		},
		handleDelete() {
			this.search_address = "";
		},
		validateBeforeSubmit() {
			const isValid = this.$refs.observer.validate();
			if (isValid) {
				this.handleAction();
			}
		},
		geoLocate() {
			navigator.geolocation.getCurrentPosition(position => {
				this.map.center = [position.coords.latitude, position.coords.longitude];
				this.markerLatLng = [
					position.coords.latitude,
					position.coords.longitude
				];
        this.getInfoByCoord(this.markerLatLng)
			});
		},
		handleSearch() {
      
			if (this.search_address) {
				console.log('vô 1')
				let location = this.search_address;
				if (
					location.split(",") &&
					location.split(",").length === 2 &&
					parseFloat(location.split(",")[0]) &&
					parseFloat(location.split(",")[1])
				) {
					console.log('vô 1.1')
					let lat = parseFloat(location.split(",")[0]);
					let lng = parseFloat(location.split(",")[1]);
					this.map.center = [lat, lng];
					this.markerLatLng = [lat, lng];
          this.getInfoByCoord(this.markerLatLng)
				} else {
					console.log('vô 1.2')
					this.initMap(location, "address");
				}
			} else {
				console.log('vô 2')
				this.initMap(this.search_address, "address");
			}
		},
		isMobile() {
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	},
	beforeMount() {}
};
</script>
<style lang="scss" scoped>
.modal-delete {
	position: fixed;
	z-index: 1030;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	padding-top: 10px;
	background: rgba(0, 0, 0, 0.6);
	.card {
		max-width: 90vw;
		width: 100%;
		height: 80vh;
		margin-bottom: 0;
		@media (max-width: 767px) {
			max-width: 100vh;
			height: 100vh;
		}
		&-header {
			border-bottom: 1px solid #dddddd;
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			padding: 40px;
			p {
				color: #333333;
				margin-bottom: 40px;
			}

			.btn__group {
				.btn {
					max-width: 150px;
					width: 100%;
					margin: 0 10px;
				}
			}
		}
		//.g-map{
		//  width: 90vw;
		//  height: 80vh;
		//  border-radius: 5px;
		//  @media (max-width: 1023px) {
		//    width: 100vw;
		//  }
		//  @media (max-width: 767px) {
		//    max-width: 100vw;
		//    height: 100vh;
		//  }
		//}
	}
}
.input-map {
	box-sizing: border-box;
	margin-right: 10px;
	width: 100%;
	//padding: 0 10px;
}
.btn {
	&-search {
		height: 40px;
		background: #faa831;
		color: #ffffff;
		font-weight: 700;
		white-space: nowrap;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		margin-right: 10px;
		margin-left: 10px;
		&:hover {
			background: #f8b24b;
		}
		span {
			margin-right: 5px;
		}
		@media (max-width: 767px) {
			width: 100%;
			margin-top: 10px;
			margin-left: 0;
		}
	}
	&-cancel {
		height: 40px;
		background: #ffffff;
		font-weight: 700;
		white-space: nowrap;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		margin-right: 10px !important;
		border-radius: 3px;
	}
	&-location {
		min-width: auto;
	}
	&-exit {
		cursor: pointer;
		margin: 10px;
		width: 20px;
		height: auto;
	}
}
.main-map {
	position: relative;
	height: 100%;
	width: 90vw;
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
.icon_marker {
	width: 25px;
}
.mini_btn {
	width: 30px;
	height: 30px;
	padding: unset !important;
}
.search-container {
	position: relative;
	margin-right: 10px;
	.icon-container {
		cursor: pointer;
		position: absolute;
		right: 0;
		top: 50%;
		width: 20px;
		height: auto;
		transform: translateY(-50%);
		margin-right: 20px;
		img {
			width: 100%;
		}
	}
}
.btn-map {
	background: #ffffff;
	border-radius: 5px;
	border: 3px solid #ffffff;
	padding: 0;
	box-sizing: border-box;
	img {
		max-width: 50px;
		height: auto;
	}
}
.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  .card {
	height: auto;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 500px;
    width: 100%;
    max-height: 62vh;
    margin-bottom: 0;
    padding: 35px 95px;
    @media (max-width: 787px) {
      padding: 20px 10px;
    }
    &-header {
      border-bottom: 1px solid #DDDDDD;
      h3 {
        color: #333333;
      }
      img {
        cursor: pointer;
      }
    }
    &-body {
      text-align: center;
      p {
        color: #333333;
        margin-bottom: 40px;
      }

      .btn__group {
        .btn {
          max-width: 150px;
          width: 100%;
          margin: 0 10px;
        }
      }
    }
  }
}

.card{
  .contain-detail{
    overflow-y: auto;
    overflow-x: hidden;
    margin-top: 20px;
    &::-webkit-scrollbar{
      width: 2px;
    }
  }
  &-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    .title{
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-table{
    border-radius: 5px;
    background: #FFFFFF;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    width: 99%;
    margin: 50px auto 50px;
  }
  &-body{
    padding: 35px 30px 40px;
  }
  &-info{
    .title{
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land{
    position: relative;
    padding: 0;
  }
}

.container-title{
  margin: -35px -95px auto;
  padding: 35px 95px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  .title{
    font-size: 1.2rem;
    @media (max-width: 767px) {
      font-size: 1.125rem;
    }
  }
  &__footer{
    margin: auto -95px -35px;
    padding: 20px 95px 20px;
    @media (max-width: 767px) {
      .btn-white{
        margin-bottom: 20px;
      }
    }
  }
}

.title{
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 25px;
  color: #000000;
}
</style>
