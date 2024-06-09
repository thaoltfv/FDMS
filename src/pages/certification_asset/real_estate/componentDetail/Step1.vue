<template>
	<div v-if="!isMobile()">
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Thông tin chung về tài sản thẩm định</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailAppraise = !showCardDetailAppraise"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailAppraise">
				<div class="container-fluid color_content">
					<div class="row">
						<div class="col-12 col-lg-7">
							<div class="row">
								<div class="col-12">
									<InputCategory
										v-model="data.general_infomation.asset_type_id"
										vid="asset_type_id"
										label="Loại tài sản"
										rules="required"
										class="form-group-container"
										:options="optionsType"
										:disabled="true"
										@change="changeAssetType($event)"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										v-model="data.general_infomation.province_id"
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
										v-model="selectedDistrictId"
										vid="district_id"
										label="Quận/Huyện"
										rules="required"
										class="form-group-container"
										:disabled="true"
										@change="changeDistrict($event)"
										:options="optionsDistrict"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										v-model="selectedWardId"
										vid="ward_id"
										label="Phường/Xã"
										rules="required"
										class="form-group-container"
										:disabled="true"
										:options="optionsWard"
										@change="changeWard($event)"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										:disabled="true"
										v-model="selectedStreetId"
										vid="street_id"
										label="Đường/Phố"
										rules="required"
										class="form-group-container"
										@change="changeStreet($event)"
										:options="optionsStreet"
									/>
								</div>
								<div class="col-12">
									<InputCategory
										:disabled="true"
										v-model="data.general_infomation.distance_id"
										vid="distance_id"
										label="Đoạn"
										class="form-group-container"
										:options="optionsDistance"
										@change="changeDistance($event)"
									/>
								</div>
								<div class="col-12">
									<InputText
										:disabledInput="true"
										v-model="data.general_infomation.full_address"
										vid="full_address"
										label="Địa chỉ"
										rules="required"
										class="form-group-container"
									/>
								</div>
								<div class="col-12">
									<InputText
										:disabledInput="true"
										v-model="data.general_infomation.appraise_asset"
										vid="appraise_asset"
										label="Tên tài sản thẩm định giá"
										rules="required"
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
										v-model="data.general_infomation.coordinates"
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
													:options="{ zoomControl: false }"
												>
													<l-tile-layer
														:url="url"
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
													<!-- <l-tile-layer :url="url"></l-tile-layer> -->
													<l-control-zoom
														position="bottomright"
													></l-control-zoom>

													<l-control position="bottomleft">
														<button
															class="btn btn-map"
															@click="handleView"
															type="button"
														>
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
													<l-control-layers
														position="bottomleft"
													></l-control-layers>
													<l-marker :lat-lng="markerLatLng">
														<l-icon
															class-name="someExtraClass"
															:iconAnchor="[30, 58]"
														>
															<img
																style="width: 60px !important"
																class="icon_marker"
																src="@/assets/images/svg_home.svg"
																alt=""
															/>
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
					<h3 class="title">Đặc điểm giao thông</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailTraffic = !showCardDetailTraffic"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailTraffic">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2">Vị trí tài sản</label>
									<div class="w-100 row">
										<div class="col-12 col-md-3 col-lg-2"></div>
										<div class="col-12 col-md-3 col-lg-2">
											<div class="d-flex">
												<input
													disabled
													type="radio"
													name="front_side"
													:value="1"
													@click="handleChangeFrontSide"
													id="front_side1"
													:checked="false"
													v-model="data.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														disabled
														class="color_content  font-weight-normal"
														style="margin-bottom: unset !important"
														for="front_side1"
														>Mặt tiền</label
													>
												</div>
											</div>
										</div>
										<div class="col-12 col-md-3 col-lg-2">
											<div class="d-flex">
												<input
													disabled
													type="radio"
													name="front_side"
													:value="0"
													@click="handleChangeAlley"
													id="front_side2"
													:checked="false"
													v-model="data.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														disabled
														class="color_content  font-weight-normal"
														style="margin-bottom: unset !important"
														for="front_side2"
														>Trong hẻm</label
													>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="col-4">
              <div class="select-group">
                <div class="d-flex">
                  <label class="select-title">Căn góc</label>
                  <div class="w-100 row">
                    <div class="col-2"></div>
                    <div class="col-5">
                      <div class="d-flex" >
                        <input disabled type="radio" name="two_sides_land" :value="true" id="two_sides_land1" :checked="false" v-model="data.traffic_infomation.two_sides_land">
                        <div style="margin-left: 0.5rem"><label disabled class="color_content" style="margin-bottom: unset !important" for="two_sides_land1">Có</label></div>
                      </div>
                    </div>
                    <div class="col-5">
                      <div class="d-flex">
                        <input type="radio" name="two_sides_land" :value="false" disabled :checked="false" id="two_sides_land2" v-model="data.traffic_infomation.two_sides_land">
                        <div style="margin-left: 0.5rem"><label disabled class="color_content" style="margin-bottom: unset !important" for="two_sides_land2">Không</label></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
						<!-- Có -->
						<div v-if="data.traffic_infomation.front_side" class="col-12">
							<div class="row content_form">
								<div class="col-12 col-md-5 col-lg-3">
									<InputLengthArea
										label="Bề rộng đường"
										:required="true"
										:disabled="true"
										rules="required"
										v-model="data.traffic_infomation.main_road_length"
										:decimal="2"
										class="form-group-container"
										@change="handleChangeRoadFrontSide"
									/>
								</div>
								<div class="col-12 col-md-7 col-lg-4">
									<InputCategory
										:disabled="true"
										v-model="data.traffic_infomation.material_id"
										label="Chất liệu đường"
										rules="required"
										class="form-group-container"
										:options="optionsMaterial"
									/>
								</div>
								<div class="infor-box pl-2 pb-0 col d-none mt-3 mr-3 d-lg-flex">
									<p>
										- <b>Bề rộng đường</b>: Chiều rộng mặt đường tiếp giáp với
										tài sản <br />
										- <b>Chất liệu đường</b>: Chất liệu mặt đường tiếp giáp với
										tài sản
									</p>
								</div>
							</div>
						</div>
						<!-- Không -->
						<div v-if="data.traffic_infomation.front_side === 0" class="col-12">
							<div
								v-for="(detail_alley, index) in data.traffic_infomation
									.property_turning_time"
								:key="index"
								class="d-flex"
							>
								<div class="w-100 row content_form">
									<div class="col-3">
										<InputText
											label="Số lần rẽ/quẹo"
											v-model="detail_alley.turning"
											vid="turning"
											rules="required"
											disabled-input
											class="form-group-container"
										/>
									</div>
									<div class="col-2">
										<InputLengthArea
											:disabled="true"
											label="Bề rộng đường"
											:required="true"
											rules="required"
											v-model="detail_alley.main_road_length"
											class="form-group-container"
											:decimal="2"
											@change="handleChangeRoadAlley($event, index)"
										/>
									</div>
									<div class="col-3">
										<InputCategory
											:disabled="true"
											v-model="detail_alley.material_id"
											label="Chất liệu đường"
											rules="required"
											class="form-group-container"
											:options="optionsMaterial"
											@change="changeMaterial"
										/>
									</div>
									<div class="col">
										<InputLengthArea
											:disabled="true"
											label="Khoảng cách đến đường chính"
											:required="true"
											rules="required"
											v-model="detail_alley.main_road_distance"
											class="form-group-container"
											:decimal="2"
											@change="handleChangeRoadDistance($event, index)"
										/>
									</div>
									<div
										v-if="
											data.traffic_infomation.property_turning_time.length > 1
										"
										class="col-1 px-3 d-flex align-items-end"
									>
										<div @click="handleDeleteTurning(index)" class="btn-delete">
											<img src="@/assets/icons/ic_delete_2.svg" alt="delete" />
										</div>
									</div>
								</div>
							</div>
							<div
								v-if="data.traffic_infomation.property_turning_time.length > 1"
								class="d-flex"
							>
								<div class="w-100 d-flex justify-content-end">
									<button
										class="btn text-warning btn-ghost btn-add"
										type="button"
										@click="handleAddTurning"
									>
										<img src="@/assets/icons/ic_add-white.svg" alt="add" />
										+ Thêm
									</button>
								</div>
								<div class="col-1 px-3"></div>
							</div>
						</div>
						<div class="col-12 ">
							<InputTextarea
								:autosize="true"
								:disableInput="true"
								v-model="data.traffic_infomation.description"
								label="Mô tả vị trí"
								class="form-group-container"
							/>
						</div>
						<div class="col-12 ">
							<InputTextarea
								:autosize="true"
								:disableInput="true"
								v-model="data.geographical_location"
								label="Vị trí địa lý"
								class="form-group-container"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Đặc điểm kinh tế - xã hội</h3>
					<img
						class="img-dropdown"
						:class="
							!showCardDetailEconomicAndSocial ? 'img-dropdown__hide' : ''
						"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="
							showCardDetailEconomicAndSocial = !showCardDetailEconomicAndSocial
						"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailEconomicAndSocial">
				<div class="container-fluid">
					<div class="row">
						<div
							v-if="businesses.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>Kinh doanh</h3>
							<div v-for="business of businesses" :key="business.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="business_id"
										:id="business.id"
										:value="business.id"
										v-model="data.economic_infomation.business_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="business.id"
											>{{ formatSentenceCase(business.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<div
							v-if="conditions.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>Cơ sở hạ tầng</h3>
							<div v-for="condition of conditions" :key="condition.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="condition_id"
										:id="condition.id"
										:value="condition.id"
										v-model="data.economic_infomation.condition_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="condition.id"
											>{{ formatSentenceCase(condition.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<div
							v-if="socialSecurities.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>An ninh trật tự, xã hội</h3>
							<div v-for="sercurity of socialSecurities" :key="sercurity.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="social_security_id"
										:id="sercurity.id"
										:value="sercurity.id"
										v-model="data.economic_infomation.social_security_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="sercurity.id"
											>{{ formatSentenceCase(sercurity.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<div
							v-if="fengshuies.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>Phong thủy</h3>
							<div v-for="fengshui of fengshuies" :key="fengshui.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="feng_shui_id"
										:id="fengshui.id"
										:value="fengshui.id"
										v-model="data.economic_infomation.feng_shui_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="fengshui.id"
											>{{ formatSentenceCase(fengshui.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<!-- <div v-if="zones.length > 0" class="col-6 form-group-container">
              <h3>Quy hoạch</h3>
              <div v-for="zone of zones" :key="zone.id">
                <div class="d-flex div_radio">
                  <input disabled type="radio" name="zoning_id" :id="zone.id" :value="zone.id" v-model="data.economic_infomation.zoning_id">
                  <div class="content_economy"><label disabled class="color_content" style="margin-bottom: unset !important" :for="zone.id">{{zone.description}}</label></div>
                </div>
              </div>
            </div> -->
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-title d-flex align-items-center justify-content-between">
				<form enctype="multipart/form-data" class="d-flex align-items-center">
					<h3 class="title">Hình ảnh</h3>
				</form>
				<img
					class="img-dropdown"
					:class="!showCardDetailImage ? 'img-dropdown__hide' : ''"
					src="@/assets/images/icon-btn-down.svg"
					alt="dropdown"
					@click="showCardDetailImage = !showCardDetailImage"
				/>
			</div>
			<div class="card-body" v-show="showCardDetailImage">
				<Tabs class="tab_contruction" :theme="theme" :navAuto="true">
					<TabItem name="Đường tiếp giáp tài sản">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div v-if="imageType" class="container-img row mr-0 ml-0">
								<div
									class="contain-img contain-img__property"
									v-if="
										imageType &&
											data.picture_infomation.length > 0 &&
											images.type_id === imageType.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imageType &&
											!data.picture_infomation.find(
												item => item.type_id === imageType.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
								</svg>
								Không có hình ảnh đường tiếp giáp tài sản
							</div>
						</div>
					</TabItem>
					<TabItem name="Tổng thể tài sản">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div v-if="imgOverall" class="container-img row mr-0 ml-0">
								<div
									class="contain-img contain-img__property"
									v-if="
										imgOverall &&
											data.picture_infomation.length > 0 &&
											images.type_id === imgOverall.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imgOverall &&
											!data.picture_infomation.find(
												item => item.type_id === imgOverall.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
								</svg>
								Không có hình ảnh tổng thể tài sản
							</div>
						</div>
					</TabItem>
					<TabItem name="Hiện trạng tài sản">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div
								v-if="imageCurrentStatus"
								class="container-img row mr-0 ml-0"
							>
								<div
									class="contain-img contain-img__property"
									v-if="
										imageCurrentStatus &&
											data.picture_infomation.length > 0 &&
											images.type_id === imageCurrentStatus.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imageCurrentStatus &&
											!data.picture_infomation.find(
												item => item.type_id === imageCurrentStatus.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
								</svg>
								Không có hình ảnh hiện trạng tài sản
							</div>
						</div>
					</TabItem>
					<!-- <TabItem name="Biên bản khảo sát hiện trạng">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageCurrentSurvey" class="container-img row mr-0 ml-0" >
                <div class="contain-img contain-img__property" v-if="imageCurrentSurvey && data.picture_infomation.length > 0 && images.type_id === imageCurrentSurvey.id" v-for="(images) in data.picture_infomation" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-if="data.picture_infomation.length === 0 || data.picture_infomation.length > 0 && imageCurrentStatus && !data.picture_infomation.find(item => item.type_id === imageCurrentSurvey.id)" class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh biên bản khảo sát hiện trạng
              </div>
            </div>
          </TabItem> -->
					<TabItem name="Khác">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div v-if="imageJuridical" class="container-img row mr-0 ml-0">
								<div
									class="contain-img contain-img__property"
									v-if="
										imageJuridical &&
											data.picture_infomation.length > 0 &&
											images.type_id === imageJuridical.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imageJuridical &&
											!data.picture_infomation.find(
												item => item.type_id === imageJuridical.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
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
			:center_map="data.general_infomation.coordinates"
			@action="handleCoordinates"
		/>
	</div>
	<div v-else>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Thông tin chung về tài sản thẩm định</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailAppraise = !showCardDetailAppraise"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailAppraise">
				<div class="container-fluid color_content">
					<div class="row">
						<div class="col-12 col-lg-7" style="padding:0;">
							<div class="row">
								<div class="col-6">
									<InputCategory
										v-model="data.general_infomation.asset_type_id"
										vid="asset_type_id"
										label="Loại tài sản"
										rules="required"
										class="form-group-container"
										:options="optionsType"
										:disabled="true"
										@change="changeAssetType($event)"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										v-model="data.general_infomation.province_id"
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
										v-model="selectedDistrictId"
										vid="district_id"
										label="Quận/Huyện"
										rules="required"
										class="form-group-container"
										:disabled="true"
										@change="changeDistrict($event)"
										:options="optionsDistrict"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										v-model="selectedWardId"
										vid="ward_id"
										label="Phường/Xã"
										rules="required"
										class="form-group-container"
										:disabled="true"
										:options="optionsWard"
										@change="changeWard($event)"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										:disabled="true"
										v-model="selectedStreetId"
										vid="street_id"
										label="Đường/Phố"
										rules="required"
										class="form-group-container"
										@change="changeStreet($event)"
										:options="optionsStreet"
									/>
								</div>
								<div class="col-6">
									<InputCategory
										:disabled="true"
										v-model="data.general_infomation.distance_id"
										vid="distance_id"
										label="Đoạn"
										class="form-group-container"
										:options="optionsDistance"
										@change="changeDistance($event)"
									/>
								</div>
								<div class="col-12">
									<InputText
										:disabledInput="true"
										v-model="data.general_infomation.full_address"
										vid="full_address"
										label="Địa chỉ"
										rules="required"
										class="form-group-container"
									/>
								</div>
								<div class="col-12">
									<InputText
										:disabledInput="true"
										v-model="data.general_infomation.appraise_asset"
										vid="appraise_asset"
										label="Tên tài sản thẩm định giá"
										rules="required"
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
										v-model="data.general_infomation.coordinates"
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
								<div class="col-12 mt-3 layer-map">
									<div
										class="d-flex all-map"
										style="padding: 0; height: 40vh;margin-top: 10px;"
									>
										<div class="main-map">
											<div id="mapid" class="layer-map">
												<l-map
													ref="map_step1"
													:zoom="map.zoom"
													:center="map.center"
													:maxZoom="20"
													:options="{ zoomControl: false }"
												>
													<l-tile-layer
														:url="url"
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
													<!-- <l-tile-layer :url="url"></l-tile-layer> -->
													<l-control-zoom
														position="bottomright"
													></l-control-zoom>

													<l-control position="bottomleft">
														<button
															class="btn btn-map"
															@click="handleView"
															type="button"
														>
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
													<l-control-layers
														position="bottomleft"
													></l-control-layers>
													<l-marker :lat-lng="markerLatLng">
														<l-icon
															class-name="someExtraClass"
															:iconAnchor="[30, 58]"
														>
															<img
																style="width: 60px !important"
																class="icon_marker"
																src="@/assets/images/svg_home.svg"
																alt=""
															/>
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
					<h3 class="title">Đặc điểm giao thông</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailTraffic = !showCardDetailTraffic"
					/>
				</div>
			</div>
			<div
				class="card-body card-info"
				v-show="showCardDetailTraffic"
				style="padding-left: 0;padding-right: 0;"
			>
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div
								class="select-group sub_header_title"
								style="padding-left: 10px;"
							>
								<div class="d-flex">
									<label class="select-title pr-2">Vị trí tài sản</label>
									<div class="w-100 row">
										<div class="col-12 col-md-3 col-lg-2"></div>
										<div class="col-6" style="padding-right:0">
											<div class="d-flex">
												<input
													disabled
													type="radio"
													name="front_side"
													:value="1"
													@click="handleChangeFrontSide"
													id="front_side1"
													:checked="false"
													v-model="data.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														disabled
														class="color_content  font-weight-normal"
														style="margin-bottom: unset !important"
														for="front_side1"
														>Mặt tiền</label
													>
												</div>
											</div>
										</div>
										<div class="col-6" style="padding:0">
											<div class="d-flex">
												<input
													disabled
													type="radio"
													name="front_side"
													:value="0"
													@click="handleChangeAlley"
													id="front_side2"
													:checked="false"
													v-model="data.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														disabled
														class="color_content  font-weight-normal"
														style="margin-bottom: unset !important"
														for="front_side2"
														>Trong hẻm</label
													>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="col-4">
              <div class="select-group">
                <div class="d-flex">
                  <label class="select-title">Căn góc</label>
                  <div class="w-100 row">
                    <div class="col-2"></div>
                    <div class="col-5">
                      <div class="d-flex" >
                        <input disabled type="radio" name="two_sides_land" :value="true" id="two_sides_land1" :checked="false" v-model="data.traffic_infomation.two_sides_land">
                        <div style="margin-left: 0.5rem"><label disabled class="color_content" style="margin-bottom: unset !important" for="two_sides_land1">Có</label></div>
                      </div>
                    </div>
                    <div class="col-5">
                      <div class="d-flex">
                        <input type="radio" name="two_sides_land" :value="false" disabled :checked="false" id="two_sides_land2" v-model="data.traffic_infomation.two_sides_land">
                        <div style="margin-left: 0.5rem"><label disabled class="color_content" style="margin-bottom: unset !important" for="two_sides_land2">Không</label></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
						<!-- Có -->
						<div v-if="data.traffic_infomation.front_side" class="col-12">
							<div class="row content_form" style="padding-left: 0;">
								<div class="col-6 col-md-5 col-lg-3">
									<InputLengthArea
										label="Bề rộng đường"
										:required="true"
										:disabled="true"
										rules="required"
										v-model="data.traffic_infomation.main_road_length"
										:decimal="2"
										class="form-group-container"
										@change="handleChangeRoadFrontSide"
									/>
								</div>
								<div class="col-6 col-md-7 col-lg-4">
									<InputCategory
										:disabled="true"
										v-model="data.traffic_infomation.material_id"
										label="Chất liệu đường"
										rules="required"
										class="form-group-container"
										:options="optionsMaterial"
									/>
								</div>
								<div class="infor-box pl-2 pb-0 col d-none mt-3 mr-3 d-lg-flex">
									<p>
										- <b>Bề rộng đường</b>: Chiều rộng mặt đường tiếp giáp với
										tài sản <br />
										- <b>Chất liệu đường</b>: Chất liệu mặt đường tiếp giáp với
										tài sản
									</p>
								</div>
							</div>
						</div>
						<!-- Không -->
						<div v-if="data.traffic_infomation.front_side === 0" class="col-12">
							<div
								v-for="(detail_alley, index) in data.traffic_infomation
									.property_turning_time"
								:key="index"
								class="d-flex"
							>
								<div class="w-100 row content_form" style="padding-left: 0;">
									<div class="col-6">
										<InputText
											label="Số lần rẽ/quẹo"
											v-model="detail_alley.turning"
											vid="turning"
											rules="required"
											disabled-input
											class="form-group-container"
										/>
									</div>
									<div class="col-6">
										<InputLengthArea
											:disabled="true"
											label="Bề rộng đường"
											:required="true"
											rules="required"
											v-model="detail_alley.main_road_length"
											class="form-group-container"
											:decimal="2"
											@change="handleChangeRoadAlley($event, index)"
										/>
									</div>
									<div class="col-6">
										<InputCategory
											:disabled="true"
											v-model="detail_alley.material_id"
											label="Chất liệu đường"
											rules="required"
											class="form-group-container"
											:options="optionsMaterial"
											@change="changeMaterial"
										/>
									</div>
									<div class="col-6">
										<InputLengthArea
											:disabled="true"
											label="Cách đường chính"
											:required="true"
											rules="required"
											v-model="detail_alley.main_road_distance"
											class="form-group-container"
											:decimal="2"
											@change="handleChangeRoadDistance($event, index)"
										/>
									</div>
									<div
										v-if="
											data.traffic_infomation.property_turning_time.length > 1
										"
										class="col-1 px-3 d-flex align-items-end"
									>
										<div @click="handleDeleteTurning(index)" class="btn-delete">
											<img src="@/assets/icons/ic_delete_2.svg" alt="delete" />
										</div>
									</div>
								</div>
							</div>
							<div
								v-if="data.traffic_infomation.property_turning_time.length > 1"
								class="d-flex"
							>
								<div class="w-100 d-flex justify-content-end">
									<button
										class="btn text-warning btn-ghost btn-add"
										type="button"
										@click="handleAddTurning"
									>
										<img src="@/assets/icons/ic_add-white.svg" alt="add" />
										+ Thêm
									</button>
								</div>
								<div class="col-1 px-3"></div>
							</div>
						</div>
						<div class="col-12 ">
							<InputTextarea
								:autosize="true"
								:disableInput="true"
								v-model="data.traffic_infomation.description"
								label="Mô tả vị trí"
								class="form-group-container"
							/>
						</div>
						<div class="col-12 ">
							<InputTextarea
								:autosize="true"
								:disableInput="true"
								v-model="data.geographical_location"
								label="Vị trí địa lý"
								class="form-group-container"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Đặc điểm kinh tế - xã hội</h3>
					<img
						class="img-dropdown"
						:class="
							!showCardDetailEconomicAndSocial ? 'img-dropdown__hide' : ''
						"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="
							showCardDetailEconomicAndSocial = !showCardDetailEconomicAndSocial
						"
					/>
				</div>
			</div>
			<div
				class="card-body card-info"
				v-show="showCardDetailEconomicAndSocial"
				style="padding-left: 0;padding-right: 0;"
			>
				<div class="container-fluid">
					<div class="row">
						<div
							v-if="businesses.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>Kinh doanh</h3>
							<div v-for="business of businesses" :key="business.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="business_id"
										:id="business.id"
										:value="business.id"
										v-model="data.economic_infomation.business_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="business.id"
											>{{ formatSentenceCase(business.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<div
							v-if="conditions.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>Cơ sở hạ tầng</h3>
							<div v-for="condition of conditions" :key="condition.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="condition_id"
										:id="condition.id"
										:value="condition.id"
										v-model="data.economic_infomation.condition_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="condition.id"
											>{{ formatSentenceCase(condition.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<div
							v-if="socialSecurities.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>An ninh trật tự, xã hội</h3>
							<div v-for="sercurity of socialSecurities" :key="sercurity.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="social_security_id"
										:id="sercurity.id"
										:value="sercurity.id"
										v-model="data.economic_infomation.social_security_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="sercurity.id"
											>{{ formatSentenceCase(sercurity.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<div
							v-if="fengshuies.length > 0"
							class="col-12 col-lg-6 form-group-container"
						>
							<h3>Phong thủy</h3>
							<div v-for="fengshui of fengshuies" :key="fengshui.id">
								<div class="d-flex div_radio">
									<input
										disabled
										type="radio"
										name="feng_shui_id"
										:id="fengshui.id"
										:value="fengshui.id"
										v-model="data.economic_infomation.feng_shui_id"
									/>
									<div class="content_economy">
										<label
											disabled
											class="color_content"
											style="margin-bottom: unset !important"
											:for="fengshui.id"
											>{{ formatSentenceCase(fengshui.description) }}</label
										>
									</div>
								</div>
							</div>
						</div>
						<!-- <div v-if="zones.length > 0" class="col-6 form-group-container">
              <h3>Quy hoạch</h3>
              <div v-for="zone of zones" :key="zone.id">
                <div class="d-flex div_radio">
                  <input disabled type="radio" name="zoning_id" :id="zone.id" :value="zone.id" v-model="data.economic_infomation.zoning_id">
                  <div class="content_economy"><label disabled class="color_content" style="margin-bottom: unset !important" :for="zone.id">{{zone.description}}</label></div>
                </div>
              </div>
            </div> -->
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-title d-flex align-items-center justify-content-between">
				<form enctype="multipart/form-data" class="d-flex align-items-center">
					<h3 class="title">Hình ảnh</h3>
				</form>
				<img
					class="img-dropdown"
					:class="!showCardDetailImage ? 'img-dropdown__hide' : ''"
					src="@/assets/images/icon-btn-down.svg"
					alt="dropdown"
					@click="showCardDetailImage = !showCardDetailImage"
				/>
			</div>
			<div
				class="card-body"
				v-show="showCardDetailImage"
				style="padding-left: 0;padding-right: 0;"
			>
				<Tabs class="tab_contruction" :theme="theme" :navAuto="true">
					<TabItem name="Đường tiếp giáp tài sản">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div v-if="imageType" class="container-img row mr-0 ml-0">
								<div
									style="width: auto;"
									class="contain-img contain-img__property"
									v-if="
										imageType &&
											data.picture_infomation.length > 0 &&
											images.type_id === imageType.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imageType &&
											!data.picture_infomation.find(
												item => item.type_id === imageType.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
								</svg>
								Không có hình ảnh đường tiếp giáp tài sản
							</div>
						</div>
					</TabItem>
					<TabItem name="Tổng thể tài sản">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div v-if="imgOverall" class="container-img row mr-0 ml-0">
								<div
									style="width: auto;"
									class="contain-img contain-img__property"
									v-if="
										imgOverall &&
											data.picture_infomation.length > 0 &&
											images.type_id === imgOverall.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imgOverall &&
											!data.picture_infomation.find(
												item => item.type_id === imgOverall.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
								</svg>
								Không có hình ảnh tổng thể tài sản
							</div>
						</div>
					</TabItem>
					<TabItem name="Hiện trạng tài sản">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div
								v-if="imageCurrentStatus"
								class="container-img row mr-0 ml-0"
							>
								<div
									style="width: auto;"
									class="contain-img contain-img__property"
									v-if="
										imageCurrentStatus &&
											data.picture_infomation.length > 0 &&
											images.type_id === imageCurrentStatus.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imageCurrentStatus &&
											!data.picture_infomation.find(
												item => item.type_id === imageCurrentStatus.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
								</svg>
								Không có hình ảnh hiện trạng tài sản
							</div>
						</div>
					</TabItem>
					<!-- <TabItem name="Biên bản khảo sát hiện trạng">
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-end mb-2">
                <h3 class="mb-0"> </h3>
              </div>
              <div v-if="imageCurrentSurvey" class="container-img row mr-0 ml-0" >
                <div class="contain-img contain-img__property" v-if="imageCurrentSurvey && data.picture_infomation.length > 0 && images.type_id === imageCurrentSurvey.id" v-for="(images) in data.picture_infomation" :key="images.id">
                  <img class="asset-img" :src="images.link" alt="img">
                </div>
              </div>
              <div v-if="data.picture_infomation.length === 0 || data.picture_infomation.length > 0 && imageCurrentStatus && !data.picture_infomation.find(item => item.type_id === imageCurrentSurvey.id)" class="infor-box">
                <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
                </svg>
                Không có hình ảnh biên bản khảo sát hiện trạng
              </div>
            </div>
          </TabItem> -->
					<TabItem name="Khác">
						<div class="mt-2">
							<div class="d-flex justify-content-between align-items-end mb-2">
								<h3 class="mb-0"></h3>
							</div>
							<div v-if="imageJuridical" class="container-img row mr-0 ml-0">
								<div
									style="width: auto;"
									class="contain-img contain-img__property"
									v-if="
										imageJuridical &&
											data.picture_infomation.length > 0 &&
											images.type_id === imageJuridical.id
									"
									v-for="images in data.picture_infomation"
									:key="images.id"
								>
									<img class="asset-img" :src="images.link" alt="img" />
								</div>
							</div>
							<div
								v-if="
									data.picture_infomation.length === 0 ||
										(data.picture_infomation.length > 0 &&
											imageJuridical &&
											!data.picture_infomation.find(
												item => item.type_id === imageJuridical.id
											))
								"
								class="infor-box"
							>
								<svg
									style="margin-right: 1rem"
									width="12"
									height="13"
									viewBox="0 0 12 13"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
										fill="#007EC6"
									/>
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
			:center_map="data.general_infomation.coordinates"
			@action="handleCoordinates"
		/>
	</div>
</template>
<style lang="scss">
@import "../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputSwitch from "@/components/Form/InputSwitch";
import InputNumberFormat from "@/components/Form/InputNumber";
import ModalDeleteIndex from "@/components/Modal/ModalDeleteIndex";
import ModalMap from "./modals/ModalMap";
import { Tabs, TabItem } from "vue-material-tabs";
import File from "@/models/File";
import {
	LMap,
	LControlZoom,
	LTileLayer,
	LMarker,
	LTooltip,
	LIcon,
	LControl,
	LControlLayers
} from "vue2-leaflet";
import Vue from "vue";
import Icon from "buefy";
import InputLengthArea from "@/components/Form/InputLengthArea.vue";

Vue.use(Icon);

export default {
	name: "Step1",
	props: [
		"data",
		"propertyTypes",
		"topographic",
		"provinces",
		"districts",
		"wards",
		"streets",
		"distances",
		"full_address",
		"full_address_street",
		"businesses",
		"conditions",
		"socialSecurities",
		"fengshuies",
		"zones",
		"materials",
		"imageDescriptions",
		"addressName"
	],
	components: {
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputNumberFormat,
		InputDatePicker,
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
		LControlLayers,
		InputLengthArea
	},
	computed: {
		optionDirection() {
			return {
				data: this.directions,
				id: "id",
				key: "description"
			};
		},
		optionFurniture() {
			return {
				data: this.furniture_list,
				id: "id",
				key: "description"
			};
		},
		optionsType() {
			return {
				data: this.propertyTypes,
				id: "id",
				key: "description"
			};
		},
		optionsTopographic() {
			return {
				data: this.topographic,
				id: "id",
				key: "description"
			};
		},
		optionsProvince() {
			return {
				data: this.provinces,
				id: "id",
				key: "name"
			};
		},
		optionsDistrict() {
			return {
				data: this.districts,
				id: "id",
				key: "name"
			};
		},
		optionsWard() {
			return {
				data: this.wards,
				id: "id",
				key: "name"
			};
		},
		optionsStreet() {
			return {
				data: this.streets,
				id: "id",
				key: "name"
			};
		},
		optionsDistance() {
			return {
				data: this.distances,
				id: "id",
				key: "name"
			};
		},
		optionsMaterial() {
			return {
				data: this.materials,
				id: "id",
				key: "description"
			};
		}
	},
	watch: {
		districts(newVal) {
			if (newVal.length > 0) {
				this.selectedDistrictId = this.data.general_infomation.district_id;
			}
		},
		wards(newVal) {
			if (newVal.length > 0) {
				this.selectedWardId = this.data.general_infomation.ward_id;
			}
		},
		streets(newVal) {
			if (newVal.length > 0) {
				this.selectedStreetId = this.data.general_infomation.street_id;
			}
		}
	},
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			showCardDetailAppraise: true,
			showCardDetailTraffic: true,
			showCardDetailEconomicAndSocial: true,
			showCardDetailImage: true,
			openModalMap: false,
			imageMap: true,
			location: {
				lng: "",
				lat: ""
			},
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 20
			},
			url:
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff",
			type: "",
			file: "",
			material: "",
			imageType: null,
			imgOverall: null,
			imageCurrentStatus: null,
			imageJuridical: null,
			imageCurrentSurvey: null,
			selectedDistrictId: null,
			selectedWardId: null,
			selectedStreetId: null,
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
			]
		};
	},
	async mounted() {
		if (this.districts.length > 0) {
			this.selectedDistrictId = this.data.general_infomation.district_id;
		}
		if (this.wards.length > 0) {
			this.selectedWardId = this.data.general_infomation.ward_id;
		}
		if (this.wards.length > 0) {
			this.selectedStreetId = this.data.general_infomation.street_id;
		}
		if (this.$refs.map_step1 && this.$refs.map_step1.mapObject) {
			this.$refs.map_step1.mapObject.invalidateSize();
		}

		await this.initMap();
		await this.getImageDescriptions(this.imageDescriptions);
	},
	methods: {
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
			// phrase.replace(/\b\w/g, function(c) {
			// return c.toUpperCase();
			// })
		},
		// formatSentenceCase (text) {
		//   return text.toLowerCase().replace(/^\b[AĂÂÁẮẤÀẰẦẢẲẨÃẴẪẠẶẬĐEÊÉẾÈỀẺỂẼỄẸỆIÍÌỈĨỊOÔƠÓỐỚÒỒỜỎỔỞÕỖỠỌỘỢUƯÚỨÙỪỦỬŨỮỤỰYÝỲỶỸỴAĂÂÁẮẤÀẰẦẢẲẨÃẴẪẠẶẬĐEÊÉẾÈỀẺỂẼỄẸỆIÍÌỈĨỊOÔƠÓỐỚÒỒỜỎỔỞÕỖỠỌỘỢUƯÚỨÙỪỦỬŨỮỤỰYÝỲỶỸỴAĂÂÁẮẤÀẰẦẢẲẨÃẴẪẠẶẬĐEÊÉẾÈỀẺỂẼỄẸỆIÍÌỈĨỊOÔƠÓỐỚÒỒỜỎỔỞÕỖỠỌỘỢUƯÚỨÙỪỦỬŨỮỤỰYÝỲỶỸỴAĂÂÁẮẤÀẰẦẢẲẨÃẴẪẠẶẬĐEÊÉẾÈỀẺỂẼỄẸỆIÍÌỈĨỊOÔƠÓỐỚÒỒỜỎỔỞÕỖỠỌỘỢUƯÚỨÙỪỦỬŨỮỤỰYÝỲỶỸỴAĂÂÁẮẤÀẰẦẢẲẨÃẴẪẠẶẬĐEÊÉẾÈỀẺỂẼỄẸỆIÍÌỈĨỊOÔƠÓỐỚÒỒỜỎỔỞÕỖỠỌỘỢUƯÚỨÙỪỦỬŨỮỤỰYÝỲỶỸỴAĂÂÁẮẤÀẰẦẢẲẨÃẴẪẠẶẬĐEÊÉẾÈỀẺỂẼỄẸỆIÍÌỈĨỊOÔƠÓỐỚÒỒỜỎỔỞÕỖỠỌỘỢUƯÚỨÙỪỦỬŨỮỤỰYÝỲỶỸỴA-Z]/g, (x) => x.toUpperCase())
		// },
		getImageDescriptions(data) {
			this.imageType = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"đường tiếp giáp tài sản thẩm định giá"
			);
			this.imgOverall = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"tổng thể tài sản thẩm định giá"
			);
			this.imageCurrentStatus = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"hiện trạng tài sản thẩm định giá"
			);
			this.imageJuridical = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() === "pháp lý tài sản"
			);
			this.imageCurrentSurvey = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"biên bản khảo sát hiện trạng"
			);
		},
		async initMap() {
			// eslint-disable-next-line no-undef
			if (this.data.general_infomation.coordinates) {
				this.map.center = [
					this.data.general_infomation.coordinates.split(",")[0],
					this.data.general_infomation.coordinates.split(",")[1]
				];
				this.markerLatLng = [
					this.data.general_infomation.coordinates.split(",")[0],
					this.data.general_infomation.coordinates.split(",")[1]
				];
				this.map.zoom = 17;
			} else {
				this.markerLatLng = [10.964112, 106.856461];
				this.map.center = [10.964112, 106.856461];
			}
		},
		changeProvince(provinceId) {
			this.$emit("getDistrict", provinceId);
		},
		changeDistrict(id) {
			this.$emit("getWardStreet", id);
		},
		changeWard(id) {
			this.$emit("getWard", id);
		},
		changeStreet(id) {
			this.$emit("changeStreet", id);
		},
		changeDistance(id) {
			this.$emit("changeDistance", id);
		},
		changeAssetType(id) {
			this.$emit("getAssetType", id);
		},
		handleChangeRoadFrontSide(value) {
			this.data.traffic_infomation.main_road_length = value;
		},
		// handle coordinates from map
		handleOpenModalMap() {
			this.openModalMap = true;
			this.key_map += 1;
		},
		handleCoordinates(coordinates) {
			this.data.general_infomation.coordinates = coordinates;
			this.location.lat = coordinates.split(",")[0];
			this.location.lng = coordinates.split(",")[1];
			this.map.center = [
				parseFloat(this.location.lat),
				parseFloat(this.location.lng)
			];
			this.markerLatLng = [
				parseFloat(this.location.lat),
				parseFloat(this.location.lng)
			];
		},
		async handleAddTurning() {
			await this.$emit("addTurning");
			await this.getTheLastTurningTime();
		},
		async handleDeleteTurning(index) {
			await this.$emit("deleteTurning", index);
			await this.getTheLastTurningTime();
		},
		handleChangeRoadDistance(value, index) {
			this.$emit("changeRoadDistance", value, index);
		},
		async handleChangeRoadAlley(value, index) {
			await this.$emit("changeRoadAlley", value, index);
			await this.getTheLastTurningTime();
		},
		handleView() {
			if (
				this.url ===
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff"
			) {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url =
					"https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.imageMap = false;
			} else {
				this.url =
					"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.imageMap = true;
			}
		},
		formatFloat(value) {
			let num = (value / 1).toFixed(2).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		titleCase(str) {
			var splitStr = str.toLowerCase().split(" ");
			for (var i = 0; i < splitStr.length; i++) {
				// You do not need to check if i is larger than splitStr length, as your for does that for you
				// Assign it back to the array
				splitStr[i] =
					splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
			}
			// Directly return the joined string
			return splitStr.join(" ");
		},
		async handleChangeAlley() {
			if (this.data.traffic_infomation.property_turning_time.length > 0) {
				await this.getTheLastTurningTime();
			}
			if (this.data.traffic_infomation.property_turning_time.length === 0) {
				await this.$emit("addTurning");
			}
		},
		async getTheLastTurningTime() {
			if (this.addressName.street) {
				const last_item =
					this.data.traffic_infomation.property_turning_time.length - 1;
				let main_road_length = this.data.traffic_infomation
					.property_turning_time[last_item].main_road_length
					? this.data.traffic_infomation.property_turning_time[last_item]
							.main_road_length
					: 0;
				let streetName = "";
				let description = "";
				streetName = this.addressName.street.toLowerCase().includes("đường")
					? this.titleCase(this.addressName.street).replace("Đường", "đường")
					: `đường ${this.titleCase(this.addressName.street)}`;
				await this.materials.forEach(material => {
					if (
						material.id ===
						this.data.traffic_infomation.property_turning_time[last_item]
							.material_id
					) {
						this.material = material.description;
					}
				});
				description =
					"Tiếp giáp " +
					(this.material ? this.material.toLowerCase() : "") +
					" rộng khoảng " +
					this.formatFloat(main_road_length) +
					"m " +
					"gần tuyến " +
					`${streetName}`;
				await this.$emit("changeDescriptionFrontSide", description);
			} else {
				// await this.$toast.open({
				//     message: 'Vui lòng chọn địa chỉ',
				//     type: 'error',
				//     position: 'top-right'
				// })
			}
		},
		handleChangeFrontSide() {
			if (this.addressName.street) {
				let streetName = this.addressName.street.toLowerCase().includes("đường")
					? this.titleCase(this.addressName.street).replace("Đường", "đường")
					: `đường ${this.titleCase(this.addressName.street)}`;
				let description = "Tiếp giáp mặt tiền " + `${streetName}`;
				this.$emit("changeDescriptionFrontSide", description);
			} else {
				this.$toast.open({
					message: "Vui lòng chọn địa chỉ",
					type: "error",
					position: "top-right"
				});
			}
		},
		async changeMaterial() {
			await this.getTheLastTurningTime();
		},
		onImageChange(e, type) {
			const typeImage = this.imageDescriptions.find(
				imageDescription => imageDescription.description.toLowerCase() === type
			);
			let files = e.target.files || e.dataTransfer.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type === "image/png" ||
					this.file.type === "image/jpeg" ||
					this.file.type === "image/jpg" ||
					this.file.type === "image/gif"
				) {
					this.type = typeImage.id;
					this.createImage();
					this.uploadImage();
				} else {
					this.$toast.open({
						message: "Hình không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
		},
		createImage() {
			let reader = new FileReader();
			let v = this;
			reader.onload = e => {
				v.image = e.target.result;
			};
			reader.readAsDataURL(this.file);
		},
		uploadImage() {
			this.isLoading = true;
			const formData = new FormData();
			formData.append("image", this.file);
			return File.upload({ data: formData }).then(response => {
				if (response && response.data) {
					const item = {
						type_id: this.type,
						link: response.data.link
					};
					// this.$emit('uploadImage', item)
					this.data.picture_infomation.push(item);
					this.isLoading = false;
				} else if (response.data.error) {
					this.isLoading = false;
					this.$toast.open({
						message: response.data.error.message,
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		removeImage(index) {
			this.data.picture_infomation.splice(index, 1);
		}
	}
};
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
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
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

.color-black {
	color: #333333;
}

.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: end;
	background: #ffffff;
	// border: 0.777778px solid #000000;
	border-radius: 5.88235px;
	padding: 0.5rem;
	// margin: auto;
	// width: 36px;
	// height: 36px;

	img {
		width: 100%;
		height: auto;
		min-width: 0.75rem;
	}
}

.btn {
	&-orange {
		background: #faa831;
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
		transition: 0.3s;
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

.text-error {
	color: #cd201f;
	font-size: 12px;
}

.select-group {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 16px 22px;

	.select-title {
		color: #00507c;
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
	border: 2px solid #617f9e;
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

.contain-img__property {
	padding-left: 0px;
	padding-bottom: 5px;
}

.asset-img {
	height: 10rem;
	border: 1px var(--primary) solid;
	border-radius: 9px;
}

.icon_marker {
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
.infor-box {
	padding: 0.75rem;
	border-radius: 12px 15px;
	background-color: #eef9ff;
	border: 1px solid #007ec6;
	color: #446b92;
	@media (max-height: 660px) {
		font-size: 12px;
	}
	@media (max-height: 970px) and (min-height: 660px) {
	}
}

.sub_header_title {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 0.5rem 2rem;
	position: relative;
	color: #00507c;
	font-weight: 700;

	.label {
		margin-right: 15px;
	}
	label {
		margin: 0;
	}
	&::before {
		content: "";
		position: absolute;
		height: calc(100% - 16px);
		width: 3px;
		background-color: #99d161;
		border-radius: 3px;
		top: 50%;
		left: 0;
		transform: translateY(-50%);
	}
}
</style>
