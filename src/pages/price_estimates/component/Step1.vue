<template>
	<div v-if="step_1">
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
								<div
									class="col-md-12 col-lg-6"
									v-if="!miscVariable.isApartment"
								>
									<InputCategory
										v-model="step_1.general_infomation.asset_type_id"
										vid="asset_type_id"
										label="Loại tài sản"
										rules="required"
										class="form-group-container"
										:options="optionsType"
										:disabled="isEdit ? true : false"
										:key="indexAssetType"
										@change="changeAssetType($event)"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										v-model="step_1.general_infomation.province_id"
										vid="province_id"
										label="Tỉnh/Thành"
										rules="required"
										class="form-group-container"
										:options="optionsProvince"
										@change="changeProvince($event)"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										v-model="step_1.general_infomation.district_id"
										vid="district_id"
										label="Quận/Huyện"
										rules="required"
										class="form-group-container"
										@change="changeDistrict($event)"
										:options="optionsDistrict"
									/>
								</div>
								<div class="col-12" v-if="miscVariable.isApartment">
									<InputCategory
										v-model="step_1.general_infomation.project_id"
										vid="project_id"
										rules="required"
										label="Tên chung cư"
										class="form-group-container"
										:options="optionsProjects"
										@change="handleChangeProject"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										v-model="step_1.general_infomation.ward_id"
										vid="ward_id"
										label="Phường/Xã"
										:disabled="miscVariable.isApartment"
										rules="required"
										class="form-group-container"
										:options="optionsWard"
										@change="changeWard($event)"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										v-if="!miscVariable.isApartment"
										v-model="step_1.general_infomation.street_id"
										vid="street_id"
										label="Đường/Phố"
										rules="required"
										class="form-group-container"
										@change="changeStreet($event)"
										:options="optionsStreet"
									/>
									<InputCategory
										v-else
										v-model="step_1.general_infomation.street_id"
										vid="street_id"
										label="Đường/Phố"
										:disabled="true"
										class="form-group-container"
										@change="changeStreet($event)"
										:options="optionsStreet"
									/>
								</div>
								<div
									class="col-md-12 col-lg-6"
									v-if="!miscVariable.isApartment"
								>
									<InputCategory
										v-model="step_1.general_infomation.distance_id"
										vid="distance_id"
										label="Đoạn"
										class="form-group-container"
										:options="optionsDistance"
										@change="changeDistance($event)"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputText
										v-model="step_1.general_infomation.address_number"
										vid="number_address"
										label="Số nhà"
										:disabledInput="miscVariable.isApartment"
										@change="priceEstimateStore.getFullAddress()"
										class="form-group-container"
									/>
								</div>
								<div class="col-md-12 col-lg-6" v-if="miscVariable.isApartment">
									<InputCategory
										v-model="step_1.general_infomation.position_by_unbd_id"
										vid="position_by_unbd_id"
										label="Vị trí theo UBND"
										:disabled="true"
										class="form-group-container"
										:options="optionsPosition"
									/>
								</div>
								<div class="col-md-6 col-lg-3" v-if="!miscVariable.isApartment">
									<InputText
										v-model="step_1.general_infomation.land_no"
										vid="land_no"
										label="Số thửa"
										@change="priceEstimateStore.getFullAddress()"
										class="form-group-container"
									/>
								</div>

								<div class="col-md-6 col-lg-3" v-if="!miscVariable.isApartment">
									<InputText
										v-model="step_1.general_infomation.doc_no"
										vid="doc_no"
										label="Số tờ"
										@change="priceEstimateStore.getFullAddress()"
										class="form-group-container"
									/>
								</div>
								<div class="col-12">
									<InputText
										v-model="step_1.general_infomation.full_address"
										vid="doc_no"
										label="Địa chỉ"
										rules="required"
										@change="getFullAddress"
										class="form-group-container"
									/>
								</div>

								<div class="col-12" v-if="!miscVariable.isApartment">
									<InputText
										v-model="step_1.general_infomation.appraise_asset"
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
										v-model="step_1.general_infomation.coordinates"
										vid="coordinates"
										label="Tọa độ"
										class="coordinates"
										rules="required"
									/>
									<div class="img-locate" v-if="!miscVariable.isApartment">
										<img
											src="@/assets/icons/ic_edit_2.svg"
											alt="locate"
											@click="handleOpenModalMap()"
										/>
									</div>
								</div>
								<!-- Map -->
								<div
									class="col-12 w-100 h-100 mt-3 d-none d-lg-block layer-map"
									style="flex: 1"
								>
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
													<!-- <l-tile-layer
                          url="https://cdn.estatemanner.com/tile/qhsdd/{z}/{x}/{y}.png"
                          :min-zoom="12"
                          :options="{ maxNativeZoom: 19, maxZoom: 20}"
                        /> -->
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
		<div class="card" v-if="!miscVariable.isApartment">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Đặc điểm tài sản</h3>
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
													type="radio"
													name="front_side"
													:value="1"
													@click="handleChangeFrontSide"
													id="front_side1"
													:checked="false"
													v-model="step_1.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														class="color_content font-weight-normal"
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
													type="radio"
													name="front_side"
													:value="0"
													@click="handleChangeAlley"
													id="front_side2"
													:checked="false"
													v-model="step_1.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														class="color_content font-weight-normal"
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

						<div v-if="step_1.traffic_infomation.front_side" class="col-12">
							<div class="row content_form">
								<div class="col-12 col-md-5 col-lg-3">
									<InputLengthArea
										label="Độ rộng đường nhỏ nhất"
										:required="true"
										rules="required"
										v-model="step_1.traffic_infomation.main_road_length"
										:decimal="2"
										class="form-group-container"
										@change="handleChangeRoadFrontSide"
									/>
								</div>
								<div class="col-12 col-md-7 col-lg-3">
									<InputCategory
										v-model="step_1.traffic_infomation.material_id"
										label="Chất liệu đường"
										rules="required"
										class="form-group-container"
										:options="optionsMaterial"
									/>
								</div>
								<div class="infor-box pl-2 pb-0 col d-none mt-3 mr-3 d-lg-flex">
									<p>
										- <b>Độ rộng đường nhỏ nhất</b>: Chiều rộng mặt đường tiếp
										giáp với tài sản <br />
										- <b>Chất liệu đường</b>: Chất liệu mặt đường tiếp giáp với
										tài sản
									</p>
								</div>
							</div>
						</div>
						<!-- Không -->
						<div
							v-if="step_1.traffic_infomation.front_side === 0"
							class="col-12"
						>
							<div
								v-for="(detail_alley, index) in step_1.traffic_infomation
									.property_turning_time"
								:key="index"
								class="d-flex"
							>
								<div class="w-100 row content_form">
									<div class="col-12 col-lg-2">
										<InputText
											label="Số lần rẽ/quẹo"
											v-model="detail_alley.turning"
											vid="turning"
											rules="required"
											disabled-input
											class="form-group-container"
										/>
									</div>
									<div class="col-12 col-lg">
										<InputLengthArea
											label="Độ rộng đường nhỏ nhất"
											:required="true"
											rules="required"
											:value="detail_alley.main_road_length"
											class="form-group-container"
											:decimal="2"
											@change="handleChangeRoadAlley($event, index)"
										/>
									</div>
									<div class="col-12 col-lg-3">
										<InputCategory
											v-model="detail_alley.material_id"
											label="Chất liệu đường"
											rules="required"
											class="form-group-container"
											:options="optionsMaterial"
											@change="changeMaterial"
										/>
									</div>
									<div class="col-12 col-lg-3">
										<InputLengthArea
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
											step_1.traffic_infomation.property_turning_time.length > 1
										"
										class="col-12 col-lg-1 px-3 d-flex align-items-end"
									>
										<div @click="handleDeleteTurning(index)" class="btn-delete">
											<img src="@/assets/icons/ic_delete_2.svg" alt="delete" />
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex">
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
								<div
									v-if="
										step_1.traffic_infomation.property_turning_time.length > 1
									"
									class="col-1 px-3"
								></div>
							</div>
						</div>
						<div class="col-12 mb-3">
							<InputTextarea
								:key="descriptTraffic"
								:autosize="true"
								:maxLength="1000"
								v-model="step_1.traffic_infomation.description"
								vid="description"
								label="Mô tả vị trí"
								class="form-group-container"
							/>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-12">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2"
										>Diện tích theo mục đích sử dụng</label
									>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-5 font-weight-bold">Mục đích sử dụng</div>
								<div class="col-3 font-weight-bold text-center">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-3 font-weight-bold">Phân mục đích</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div
								v-for="(main_land, index) in step_1.total_area"
								:key="index"
								class=" mb-2 row content_form"
							>
								<div class="col-5">
									<InputCategoryCustom
										v-model="main_land.land_type_purpose_id"
										vid="land_type_purpose_id"
										nonLabel="Mục đích sử dụng"
										rules="required"
										:options="optionsTypePurposes"
										@change="changeLandTypePurpose(index, $event, main_land)"
									/>
								</div>
								<div :key="renderInputMainArea" class="col-3">
									<InputAreaCustom
										v-model="main_land.total_area"
										:decimal="2"
										vid="total_area"
										:required="true"
										:max="9999999999999999"
										:min="0"
										rules="required"
										:sufix="false"
										:text_center="true"
										nonLabel="Diện tích phù hợp"
										@change="handleChangeMainArea($event, index)"
										:errorCustom="validateArea(main_land.land_type_purpose_id)"
									/>
								</div>
								<div class="col-3">
									<InputCategoryBoolean
										v-model="main_land.is_transfer_facility"
										vid="is_transfer_facility"
										nonLabel="Phân mục đích"
										:options="optionMainOrNot"
										:showError="checkFacility(main_land.is_transfer_facility)"
										:disabled="step_1.total_area.length === 1"
									/>
								</div>
								<div class="col-1 px-3 d-flex align-items-end">
									<div @click="handleDeleteMainArea(index)" class="btn-delete">
										<img src="@/assets/icons/ic_delete_2.svg" alt="delete" />
									</div>
								</div>
							</div>
						</div>
						<div class="d-flex">
							<div class="d-flex justify-content-end">
								<button
									class="btn text-warning btn-ghost btn-add"
									type="button"
									@click="handleAddMainArea"
								>
									<img src="@/assets/icons/ic_add-white.svg" alt="add" />
									+ Thêm
								</button>
							</div>
							<div class="col-1 px-3"></div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-12">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2"
										>Phần diện tích vi phạm quy hoạch</label
									>
									<!-- <label class="input-checkbox">
										<input
											id="vio_land"
											type="checkbox"
											:key="updateCheckBox"
											value="checkShowPlanning"
											v-model="checkShowPlanning"
											@change="handleChangeStatusPlanning"
										/>
										<span class="check-mark" />
									</label> -->
								</div>
							</div>
						</div>
						<!-- <div class="col-12 mt-3">
							<div
								v-if="step_1.planning_area.length == 0"
								class="infor-box pl-2 w-100"
							>
								<span class="mr-1">
									<svg
										width="13"
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
								</span>
								Tài sản thẩm định chưa khai báo diện tích vi phạm quy hoạch!
							</div>
						</div> -->
						<div v-if="step_1.planning_area.length > 0" class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-5 font-weight-bold">Mục đích sử dụng</div>
								<div class="col-3 font-weight-bold text-center">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-3 font-weight-bold">Loại quy hoạch</div>
							</div>
						</div>
						<div v-if="step_1.planning_area.length > 0" class="col-12 mt-2">
							<div
								v-for="(planning_area, index) in step_1.planning_area"
								:key="index"
								class="mb-2 row content_form"
							>
								<div class="col-5">
									<InputCategoryCustom
										v-model="planning_area.land_type_purpose_id"
										vid="land_type_purpose_id"
										nonLabel="Mục đích sử dụng"
										rules="required"
										:options="optionsTypePurposes"
										@change="
											changeLandPlanningPurpose(index, $event, planning_area)
										"
									/>
								</div>
								<div class="col-3">
									<InputAreaCustom
										v-model="planning_area.planning_area"
										:decimal="2"
										:max="9999999999999999"
										:min="0"
										vid="planning_area"
										:required="true"
										rules="required"
										:sufix="false"
										:text_center="true"
										nonLabel="Diện tích vi phạm"
										@change="handleChangePlanningArea($event, index)"
										:errorCustom="
											validateArea(planning_area.land_type_purpose_id)
										"
									/>
								</div>
								<div class="col-3">
									<div class="col-12">
										<InputText
											v-model="planning_area.type_zoning"
											vid="appraise_asset"
											nonLabel="Loại quy hoạch"
											rules="required"
										/>
									</div>
								</div>
								<div class="col-1 px-3 d-flex align-items-end">
									<div
										@click="handleDeletePlanningArea(index)"
										class="btn-delete"
									>
										<img src="@/assets/icons/ic_delete_2.svg" alt="delete" />
									</div>
								</div>
							</div>
						</div>
						<div class="d-flex">
							<div class="w-100 d-flex justify-content-end">
								<button
									class="btn text-warning btn-ghost btn-add"
									type="button"
									@click="handleAddPlanningArea"
								>
									<img src="@/assets/icons/ic_add-white.svg" alt="add" />
									+ Thêm
								</button>
							</div>
							<div class="col-1 px-3"></div>
						</div>
					</div>
				</div>
			</div>
			<div style="z-index: 1" class="card-footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="row content_form">
								<div class="col-5">
									<label style="margin-top: 0.5rem" class="font-weight-bold">
										Tổng diện tích thẩm định (m<sup>2</sup>)
									</label>
								</div>
								<div class="col-3 result_total_appraise">
									<strong>{{
										formatNumber(
											parseFloat(
												step_1.land_details.appraise_land_sum_area
											).toFixed(2)
										)
									}}</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card" v-else>
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Chi tiết căn hộ</h3>
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
						<InputCategory
							v-model="step_1.apartment_properties.block_id"
							vid="block_id"
							rules="required"
							label="Block (khu)"
							class="col-12 col-lg-4 form-group-container"
							:options="optionsBlocks"
							@change="handleChangeBlock"
						/>
						<InputCategory
							v-model="step_1.apartment_properties.floor_id"
							vid="floor_id"
							rules="required"
							label="Tầng"
							class="col-12 col-lg-4 form-group-container"
							:options="optionsFloors"
							@change="handleChangeFloor"
						/>
						<InputText
							v-model="step_1.apartment_properties.apartment_name"
							vid="apartment_name"
							label="Mã căn hộ"
							rules="required"
							class="col-12 col-lg-4 form-group-container"
							@change="changeAparment"
						/>
						<InputArea
							v-model="step_1.apartment_properties.area"
							vid="area"
							label="Diện tích (m²)"
							rules="required"
							:max="99999999"
							@change="step_1.apartment_properties.area = $event"
							class="col-12 col-lg-4 form-group-container"
						/>

						<InputNumberNoneFormat
							v-model="step_1.apartment_properties.bedroom_num"
							vid="bedroom_num"
							label="Số phòng ngủ"
							:max="9999"
							@change="step_1.apartment_properties.bedroom_num = $event"
							class="col-12 col-lg-4 form-group-container"
						/>
						<InputNumberNoneFormat
							v-model="step_1.apartment_properties.wc_num"
							vid="wc_num"
							label="Số phòng WC"
							:max="9999"
							@change="step_1.apartment_properties.wc_num = $event"
							class="col-12 col-lg-4 form-group-container"
						/>
						<InputCategory
							v-model="step_1.apartment_properties.handover_year"
							class="col-12 col-lg-4 form-group-container"
							vid="handover_year"
							label="Năm sử dụng"
							:options="optionYearBuild"
						/>
						<InputCategory
							v-model="step_1.apartment_properties.direction_id"
							vid="direction_id"
							label="Hướng chính"
							class="col-12 col-lg-4 form-group-container"
							:options="optionDirection"
						/>

						<InputCategory
							v-model="step_1.apartment_properties.furniture_quality_id"
							vid="furniture_quality_id"
							label="Tình trạng nội thất"
							class="col-12 col-lg-4 form-group-container"
							:options="optionFurniture"
						/>

						<InputText
							v-model="step_1.general_infomation.appraise_asset"
							vid="data.appraise_asset"
							label="Tên căn hộ"
							rules="required"
							class="col-12 form-group-container"
						/>
						<InputTextarea
							label="Mô tả"
							v-model="step_1.apartment_properties.description"
							vid="description"
							:rows="4"
							:maxLength="1000"
							class="col-12 form-group-container"
						/>
					</div>
				</div>
			</div>
		</div>
		<ModalMap
			v-if="openModalMap"
			@cancel="openModalMap = false"
			:location="location"
			:address="miscInfo.full_address_street"
			:center_map="step_1.general_infomation.coordinates"
			@action="handleCoordinates"
		/>
	</div>
</template>
<style lang="scss">
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputSwitch from "@/components/Form/InputSwitch";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCategoryBoolean from "@/components/Form/InputCategoryBoolean";
import InputCategoryCustom from "@/components/Form/InputCategoryCustom";
import InputAreaCustom from "@/components/Form/InputAreaCustom";
import InputArea from "@/components/Form/InputArea";
import InputNumberNoneFormat from "@/components/Form/InputNumberNoneFormat";

import ModalDeleteIndex from "@/components/Modal/ModalDeleteIndex";
import ModalMap from "./modals/ModalMap";
import { Tabs, TabItem } from "vue-material-tabs";
import File from "@/models/File";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

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
import _ from "lodash";

Vue.use(Icon);
export default {
	name: "Step1",
	props: ["isEdit"],
	components: {
		InputArea,
		InputNumberNoneFormat,
		InputAreaCustom,
		InputCategoryCustom,
		InputCategoryBoolean,
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
		LControlLayers,
		LIcon,
		InputLengthArea
	},
	setup() {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const isMobile = ref(checkMobile());

		const priceEstimateStore = usePriceEstimatesStore();
		const {
			priceEstimates,
			miscInfo,
			addressName,
			dataInfo,
			miscVariable
		} = storeToRefs(priceEstimateStore);
		// const step_1 = ref(_.cloneDeep(priceEstimates.value.step_1));
		const step_1 = ref(priceEstimates.value.step_1);
		const checkShowPlanning = ref(false);
		const built_years = ref([]);
		if (miscVariable.value.isApartment) {
			const year = new Date().getFullYear();
			for (let i = 1970; i <= year; i++) {
				built_years.value.push({
					year: i
				});
			}
		}
		return {
			miscVariable,
			step_1,
			isMobile,
			miscInfo,
			dataInfo,
			addressName,
			priceEstimates,
			priceEstimateStore,
			checkShowPlanning,
			built_years
		};
	},
	computed: {
		optionYearBuild() {
			return {
				data: this.built_years,
				id: "year",
				key: "year"
			};
		},
		optionFurniture() {
			return {
				data: this.miscInfo.furniture_list,
				id: "id",
				key: "description"
			};
		},
		optionsPosition() {
			return {
				data: this.miscInfo.points,
				id: "id",
				key: "description"
			};
		},
		optionDirection() {
			return {
				data: this.miscInfo.directions,
				id: "id",
				key: "description"
			};
		},
		optionsProjects() {
			return {
				data: this.miscInfo.projects,
				id: "id",
				key: "name"
			};
		},
		optionsBlocks() {
			return {
				data: this.miscInfo.blocks,
				id: "id",
				key: "name"
			};
		},
		optionsFloors() {
			return {
				data: this.miscInfo.floors,
				id: "id",
				key: "name"
			};
		},
		optionsTypePurposes() {
			return {
				data: this.miscInfo.type_purposes,
				id: "id",
				key: "description"
			};
		},
		optionMainOrNot() {
			return {
				data: this.miscInfo.optionMainChoose,
				id: "id",
				key: "description"
			};
		},
		optionsType() {
			return {
				data: this.miscInfo.propertyTypes,
				id: "id",
				key: "description"
			};
		},
		optionsTopographic() {
			return {
				data: this.miscInfo.topographic,
				id: "id",
				key: "description"
			};
		},
		optionsProvince() {
			// console.log('llll tỉnh', this.miscInfo.provinces)
			return {
				data: this.miscInfo.provinces,
				id: "id",
				key: "name"
			};
		},
		optionsDistrict() {
			return {
				data: this.miscInfo.districts,
				id: "id",
				key: "name"
			};
		},
		optionsWard() {
			return {
				data: this.miscInfo.wards,
				id: "id",
				key: "name"
			};
		},
		optionsStreet() {
			return {
				data: this.miscInfo.streets,
				id: "id",
				key: "name"
			};
		},
		optionsDistance() {
			return {
				data: this.miscInfo.distances,
				id: "id",
				key: "name"
			};
		},
		optionsMaterial() {
			return {
				data: this.miscInfo.materials,
				id: "id",
				key: "description"
			};
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
			descriptTraffic: 11232,
			updateCheckBox: 1200,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			url:
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff",
			type: "",
			file: "",
			renderInputMainArea: 23123123,
			material: "",
			imageType: null,
			imgOverall: null,
			imageCurrentStatus: null,
			imageJuridical: null,
			indexAssetType: 986,
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
		if (this.$refs.map_step1 && this.$refs.map_step1.mapObject) {
			this.$nextTick(() => {
				this.$refs.map_step1.mapObject.invalidateSize();
			});
		}
		await this.initMap();
		await this.getImageDescriptions(this.miscInfo.imageDescriptions);
	},
	methods: {
		getFullAddress() {
			// if (this.miscVariable.isApartment) {
			// 	this.step_1.appraise_asset = this.getFullName();
			// 	this.step_1.apartment_properties.accessibility = this.getAccessibility();
			// }
		},
		getFullName() {
			let projectName = "";
			let blockName = "";
			let floorName = "";
			const apartmentName = this.step_1.apartment_properties.apartment_name
				? "Căn hộ số " + this.step_1.apartment_properties.apartment_name + ", "
				: "";
			if (
				this.miscInfo.projects.length > 0 &&
				this.step_1.general_infomation.project_id
			) {
				const project = this.miscInfo.projects.find(
					i => i.id === this.step_1.general_infomation.project_id
				);
				projectName = project ? "chung cư " + project.name + ", " : "";
			}
			if (
				this.miscInfo.blocks.length > 0 &&
				this.step_1.apartment_properties.block_id
			) {
				const block = this.miscInfo.blocks.find(
					i => i.id === this.step_1.apartment_properties.block_id
				);
				blockName = block ? "khu " + block.name + ", " : "";
			}
			if (
				this.miscInfo.floors.length > 0 &&
				this.step_1.apartment_properties.floor_id
			) {
				const floor = this.miscInfo.floors.find(
					i => i.id === this.step_1.apartment_properties.floor_id
				);
				floorName = floor ? "tầng " + floor.name + ", " : "";
			}
			this.step_1.general_infomation.full_address =
				apartmentName +
				floorName +
				blockName +
				projectName +
				"địa chỉ: " +
				this.step_1.general_infomation.full_address_street;
			return "Quyền sở hữu " + this.step_1.general_infomation.full_address;
		},
		handleChangeProject(event) {
			this.priceEstimateStore.handleChangeProject(event);
			this.step_1.apartment_properties.accessibility = this.getAccessibility();
			this.step_1.general_infomation.appraise_asset = this.getFullName();
			// this.getPlaninginfo();
		},
		changeFulladdress(event) {
			this.step_1.general_infomation.appraise_asset = this.getFullName();
			this.step_1.apartment_properties.accessibility = this.getAccessibility();
		},
		handleChangeBlock(event) {
			this.step_1.general_infomation.appraise_asset = this.getFullName();
			this.step_1.apartment_properties.accessibility = this.getAccessibility();
			this.priceEstimateStore.handleChangeBlock(event);
		},
		handleChangeFloor(event) {
			this.step_1.apartment_properties.floor_id = event;
			this.step_1.general_infomation.appraise_asset = this.getFullName();
			this.step_1.apartment_properties.accessibility = this.getAccessibility();
		},
		changeAparment() {
			this.step_1.general_infomation.appraise_asset = this.getFullName();
			this.step_1.apartment_properties.accessibility = this.getAccessibility();
			// this.$emit('changeAparment')
		},
		getAccessibility() {
			let projectName = "";
			let blockName = "";
			let floorName = "";
			const apartmentName = this.step_1.apartment_properties.apartment_name
				? "Căn hộ số " + this.step_1.apartment_properties.apartment_name + ", "
				: "";
			if (
				this.miscInfo.projects.length > 0 &&
				this.step_1.general_infomation.project_id
			) {
				const project = this.miscInfo.projects.find(
					i => i.id === this.step_1.general_infomation.project_id
				);
				projectName = project ? "chung cư " + project.name + ", " : "";
			}
			if (
				this.miscInfo.blocks.length > 0 &&
				this.step_1.apartment_properties.block_id
			) {
				const block = this.miscInfo.blocks.find(
					i => i.id === this.step_1.apartment_properties.block_id
				);
				blockName = block ? "khu " + block.name + ", " : "";
			}
			if (
				this.miscInfo.floors.length > 0 &&
				this.step_1.apartment_properties.floor_id
			) {
				const floor = this.miscInfo.floors.find(
					i => i.id === this.step_1.apartment_properties.floor_id
				);
				floorName = floor ? "tầng " + floor.name + ", " : "";
			}
			this.step_1.general_infomation.full_address =
				apartmentName +
				floorName +
				blockName +
				projectName +
				"địa chỉ: " +
				this.step_1.general_infomation.full_address_street;
			return (
				"Tài sản thẩm định là " + this.step_1.general_infomation.full_address
			);
		},
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
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
		},
		async initMap() {
			// eslint-disable-next-line no-undef
			if (this.step_1.general_infomation.coordinates) {
				this.map.center = [
					this.step_1.general_infomation.coordinates.split(",")[0],
					this.step_1.general_infomation.coordinates.split(",")[1]
				];
				this.markerLatLng = [
					this.step_1.general_infomation.coordinates.split(",")[0],
					this.step_1.general_infomation.coordinates.split(",")[1]
				];
				this.map.zoom = 17;
			} else {
				this.markerLatLng = [10.964112, 106.856461];
				this.map.center = [10.964112, 106.856461];
			}
		},
		changeProvince(provinceId) {
			this.priceEstimateStore.changeProvince(provinceId);
		},
		changeDistrict(id) {
			this.priceEstimateStore.changeDistrict(id);
		},
		changeWard(id) {
			this.priceEstimateStore.findWard(id);
		},
		changeStreet(id) {
			this.priceEstimateStore.changeStreet(id);
		},
		changeDistance(id) {
			this.priceEstimateStore.findDistance(id);
		},
		changeAssetType(id) {
			if (id === 39) {
				this.step_1.general_infomation.asset_type_id = "";
				this.$toast.open({
					message: "Hiện tại chức năng này chưa được mở ở phiên bản dùng thử",
					type: "error",
					position: "top-right",
					duration: 5000
				});
			} else {
				this.step_1.general_infomation.asset_type_id = id;
				this.indexAssetType++;
				this.priceEstimateStore.changeAssetType(id);
			}
		},
		handleChangeRoadFrontSide(value) {
			this.step_1.traffic_infomation.main_road_length = value;
		},
		// handle coordinates from map
		handleOpenModalMap() {
			this.openModalMap = true;
			this.key_map += 1;
		},
		handleCoordinates(coordinates) {
			this.step_1.general_infomation.coordinates = coordinates;
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
		addTurning() {
			this.step_1.traffic_infomation.property_turning_time.push({
				is_alley_with_connection: "",
				main_road_distance: "",
				main_road_length: "",
				material_id: "",
				turning:
					"Hẻm số " +
					(this.step_1.traffic_infomation.property_turning_time.length + 1)
			});
		},
		async handleAddTurning() {
			this.addTurning();
			await this.getTheLastTurningTime();
		},

		async handleDeleteTurning(index) {
			this.step_1.traffic_infomation.property_turning_time.splice(index, 1);
			await this.getTheLastTurningTime();
		},
		async handleChangeRoadDistance(value, index) {
			this.step_1.traffic_infomation.property_turning_time[
				index
			].main_road_distance = value;
			await this.getTheLastTurningTime();
		},
		async handleChangeRoadAlley(value, index) {
			this.step_1.traffic_infomation.property_turning_time[
				index
			].main_road_length = value;
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
			if (this.step_1.traffic_infomation.property_turning_time.length > 0) {
				this.step_1.traffic_infomation.main_road_length = null;
				await this.getTheLastTurningTime();
			}
			if (this.step_1.traffic_infomation.property_turning_time.length === 0) {
				this.addTurning();
			}
		},
		async getTheLastTurningTime() {
			if (this.dataInfo.streetName) {
				const last_item =
					this.step_1.traffic_infomation.property_turning_time.length - 1;

				let main_road_length = this.step_1.traffic_infomation
					.property_turning_time[last_item].main_road_length
					? this.step_1.traffic_infomation.property_turning_time[last_item]
							.main_road_length
					: 0;
				let streetName = "";
				let main_road_distance = this.step_1.traffic_infomation
					.property_turning_time[last_item].main_road_distance
					? this.step_1.traffic_infomation.property_turning_time[last_item]
							.main_road_distance
					: 0;
				let description = "";
				streetName = this.dataInfo.streetName.toLowerCase().includes("đường")
					? this.titleCase(this.dataInfo.streetName).replace("Đường", "đường")
					: `đường ${this.titleCase(this.dataInfo.streetName)}`;
				await this.miscInfo.materials.forEach(material => {
					if (
						material.id ===
						this.step_1.traffic_infomation.property_turning_time[last_item]
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
					"cách tuyến " +
					`${streetName}` +
					" khoảng " +
					this.formatFloat(main_road_distance) +
					"m";
				this.step_1.traffic_infomation.description = description;
				this.descriptTraffic++;
			} else {
			}
		},
		handleChangeFrontSide() {
			this.step_1.traffic_infomation.property_turning_time = [];
			if (this.dataInfo.streetName) {
				let streetName = this.dataInfo.streetName
					.toLowerCase()
					.includes("đường")
					? this.titleCase(this.dataInfo.streetName).replace("Đường", "đường")
					: `đường ${this.titleCase(this.dataInfo.streetName)}`;
				let description = "Tiếp giáp mặt tiền " + `${streetName}`;
				this.step_1.traffic_infomation.description = description;
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
			const typeImage = this.miscInfo.imageDescriptions.find(
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
						link: response.data.data.link
					};
					// this.$emit('uploadImage', item)
					this.step_1.picture_infomation.push(item);
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
			this.step_1.picture_infomation.splice(index, 1);
		},
		addMainArea() {
			let checkIsCheckFacility = this.step_1.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length > 0) {
				this.step_1.total_area.push({
					land_type_purpose_id: "",
					total_area: "",
					is_transfer_facility: false
				});
			} else {
				this.step_1.total_area.push({
					land_type_purpose_id: "",
					total_area: "",
					is_transfer_facility: null
				});
			}
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		deleteMainArea(index) {
			if (this.step_1.total_area.length > 1) {
				this.step_1.total_area.splice(index, 1);
				// this.step_1.UBND_price.splice(index, 1);
			} else {
				this.$toast.open({
					message: "Diện tích phù hợp quy hoạch không được rỗng",
					type: "error",
					position: "top-right"
				});
			}
			let checkIsCheckFacility = this.step_1.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length === 0) {
				this.step_1.total_area[0].is_transfer_facility = true;
			}
			// console.log('mục đích',this.step_1.total_area)
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
		},
		addPlanningArea() {
			this.step_1.planning_area.push({
				land_type_purpose_id: "",
				planning_area: "",
				type_zoning: "",
				land_type_purpose: {}
			});
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		deletePlanningArea(index) {
			if (this.step_1.planning_area.length > 0) {
				this.step_1.planning_area.splice(index, 1);
			}
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
		},
		async changeLandTypePurpose(index, land_type_purpose_id) {
			if (land_type_purpose_id) {
				// set land_type_purpose for total_area
				let getDataTypePrupose = this.miscInfo.type_purposes.filter(
					item => item.id === land_type_purpose_id
				);
				if (getDataTypePrupose && getDataTypePrupose.length > 0) {
					this.step_1.total_area[index].land_type_purpose =
						getDataTypePrupose[0];
				}
			} else {
				this.step_1.total_area[index].land_type_purpose = {};
			}
			await this.handleChangeUBNDPrice();
			await this.handleGetTotalArea();
		},
		async changeLandPlanningPurpose(index, land_type_purpose_id) {
			if (land_type_purpose_id) {
				// set land_type_purpose for planning_area
				let getDataTypePrupose = this.miscInfo.type_purposes.filter(
					item => item.id === land_type_purpose_id
				);
				if (getDataTypePrupose && getDataTypePrupose.length > 0) {
					this.step_1.planning_area[index].land_type_purpose =
						getDataTypePrupose[0];
				} else {
					this.step_1.planning_area[index].land_type_purpose = {};
				}
			}
			await this.handleChangeUBNDPrice();
			await this.handleGetTotalArea();
		},

		handleChangeUBNDPrice() {
			const map = new Map();
			const map1 = new Map();
			let allPurposeArray = [];
			// check total_area and create data UBND_price
			this.step_1.total_area.forEach(itemMainArea => {
				if (!map.has(itemMainArea.land_type_purpose_id)) {
					map.set(itemMainArea.land_type_purpose_id);
					allPurposeArray.push({
						position_type_id: "",
						circular_unit_price: "",
						land_type_purpose_id: itemMainArea.land_type_purpose_id,
						land_type_purpose: itemMainArea.land_type_purpose
					});
				}
			});
			this.step_1.planning_area.forEach(itemPlanningArea => {
				if (!map1.has(itemPlanningArea.land_type_purpose_id)) {
					map1.set(itemPlanningArea.land_type_purpose_id);
					let checkUBNDPrice1 = allPurposeArray.filter(
						item =>
							item.land_type_purpose_id ===
							itemPlanningArea.land_type_purpose_id
					);
					if (
						checkUBNDPrice1 &&
						checkUBNDPrice1.length === 0 &&
						itemPlanningArea.land_type_purpose_id
					) {
						allPurposeArray.push({
							position_type_id: "",
							circular_unit_price: "",
							land_type_purpose_id: itemPlanningArea.land_type_purpose_id,
							land_type_purpose: itemPlanningArea.land_type_purpose
						});
					}
				}
			});
			// allPurposeArray.forEach(itemPurpose => {
			// 	let checkUBNDIsExist = this.step_1.UBND_price.filter(
			// 		itemUBND =>
			// 			itemUBND.land_type_purpose_id === itemPurpose.land_type_purpose_id
			// 	);
			// 	if (checkUBNDIsExist && checkUBNDIsExist.length > 0) {
			// 		itemPurpose.position_type_id = checkUBNDIsExist[0].position_type_id;
			// 		itemPurpose.circular_unit_price =
			// 			checkUBNDIsExist[0].circular_unit_price;
			// 	}
			// });
			// this.step_1.UBND_price = allPurposeArray;
		},

		handleChangeMainArea(value, index) {
			if (value) {
				this.step_1.total_area[index].total_area = value;
				this.handleGetTotalArea();
			}
		},
		handleChangePlanningArea(value, index) {
			if (value) {
				this.step_1.planning_area[index].planning_area = value;
				this.handleGetTotalArea();
			}
		},
		handleGetTotalArea() {
			let total = 0;
			let map = new Map();
			this.step_1.total_area.forEach(item => {
				if (!map.has(item.land_type_purpose_id)) {
					map.set(item.land_type_purpose_id, item.total_area);
				}
				total += item.total_area;
			});
			this.step_1.planning_area.forEach(item => {
				if (!map.has(item.land_type_purpose_id)) {
					total += item.planning_area;
				}
			});
			this.step_1.land_details.appraise_land_sum_area = +total;
		},
		changeStatusPlanning(status) {
			if (status) {
				this.step_1.planning_area.push({
					land_type_purpose_id: "",
					planning_area: "",
					type_zoning: ""
				});
			} else {
				this.step_1.planning_area = [];
				this.handleGetTotalArea();
				this.handleChangeUBNDPrice();
			}
		},
		validateArea(landTypePurposeId) {
			let error = "";
			if (landTypePurposeId) {
				let planningAreaData = this.step_1.planning_area.filter(
					item => item.land_type_purpose_id === landTypePurposeId
				);
				let totalAreaData = this.step_1.total_area.filter(
					item => item.land_type_purpose_id === landTypePurposeId
				);
				if (
					planningAreaData &&
					totalAreaData &&
					planningAreaData.length > 0 &&
					totalAreaData.length > 0
				) {
					let planningArea = planningAreaData[0].planning_area;
					let totalArea = totalAreaData[0].total_area;
					if (planningArea > totalArea)
						error = "Diện tích vi phạm không được lớn hơn diện tích sử dụng";
					else error = "";
				} else error = "";
			} else error = "";
			this.miscInfo.step1AreaValidate = error;

			return error;
		},

		handleDeleteMainArea(index) {
			if (this.step_1.total_area.length > 1) {
				this.step_1.total_area.splice(index, 1);
				// this.step_1.UBND_price.splice(index, 1);
			} else {
				this.$toast.open({
					message: "Diện tích phù hợp quy hoạch không được rỗng",
					type: "error",
					position: "top-right"
				});
			}
			let checkIsCheckFacility = this.step_1.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length === 0) {
				this.step_1.total_area[0].is_transfer_facility = true;
			}
			// console.log('mục đích',this.step_1.total_area)
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
			this.renderInputMainArea += 1;
		},
		handleAddMainArea() {
			let checkIsCheckFacility = this.step_1.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length > 0) {
				this.step_1.total_area.push({
					land_type_purpose_id: "",
					total_area: "",
					is_transfer_facility: false
				});
			} else {
				this.step_1.total_area.push({
					land_type_purpose_id: "",
					total_area: "",
					is_transfer_facility: true
				});
			}
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		handleChangeStatusPlanning(event) {
			if (event.target.checked) {
				this.changeStatusPlanning(true);
			} else {
				this.showConfirmPlanning = true;
				this.message =
					"Tất cả thông tin diện tích vi phạm quy hoạch sẽ bị xóa, bạn có chắc muôn bỏ phần diện tích quy hoạch đã nhập";
			}
		},
		handleDeletePlanningArea(index) {
			if (this.step_1.planning_area.length > 0) {
				this.step_1.planning_area.splice(index, 1);
			}
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
		},
		handleAddPlanningArea() {
			this.step_1.planning_area.push({
				land_type_purpose_id: "",
				planning_area: "",
				type_zoning: "",
				land_type_purpose: {}
			});
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		checkFacility(facility) {
			if (facility) {
				let checkFacility = this.step_1.total_area.filter(
					item => item.is_transfer_facility === true
				);
				if (checkFacility && checkFacility.length > 1) {
					return true;
				} else return false;
			} else return false;
		},

		formatNumber(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
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

.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: end;
	background: #ffffff;
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

// .text-error {
//   color: #cd201f;
//   font-size: 14pxfont-size: 14px;
// }

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
.container_input_img {
	background-image: url("../../../assets/images/add_img.png");
	background-repeat: no-repeat;
	background-size: cover;
	background-color: transparent;
	border: 2px solid #617f9e;
	border-radius: 10px;
	min-width: 10rem;
	min-height: 10rem;
	// cursor: pointer;
	position: relative;
	margin-bottom: 5px;
}
.input_file_4 {
	left: 0;
	opacity: 0;
	width: 100%;
	height: 100%;
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

.sub_header_title {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 0.5rem 2rem;
	position: relative;
	color: #00507c;
	font-weight: 700;
	font-size: 1.125rem;
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
.result_total_appraise {
	text-align: center;
	background: #eef9ff;
	border: 1px solid #007ec6;
	border-radius: 3px;
	padding-top: 0.5rem;
	padding-bottom: 0.4rem;
}
.delete {
	background: #617f9e;
	color: #ffffff;
	width: 23px;
	height: 26px;
	text-align: center;
	line-height: 1.5;
	cursor: pointer;
	font-weight: 700;
	position: absolute;
	border-radius: 4px;
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
.infor-box {
	padding: 1rem;
	border-radius: 12px 15px;
	background-color: #eef9ff;
	border: 1px solid #007ec6;
	color: #446b92;
	@media (max-height: 660px) {
		font-size: 12px;
	}
	@media (max-height: 970px) and (min-height: 660px) {
		font-size: 14px;
	}
}
</style>
