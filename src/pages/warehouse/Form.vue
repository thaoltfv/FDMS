<template>
	<div class="pannel">
		<ValidationObserver tag="form"
												ref="observer"
												@submit.prevent="validateBeforeSubmit">
			<div class="position-relative info-container">
				<div class="card">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">Thông tin giao dịch</h3>
							<div v-if="form.id" class=" color_content card-status">
								{{form.id ? `TSSS_${form.id}` : 'TSSS'}}
							</div>
						</div>
					</div>
					<div class="card-body card-info" v-if="showInfo">
						<div class="container-fluid">
							<div class="row">
								<!-- <div class="col-12 col-md-6 col-lg-3 form-group-container">
									<label class="font-weight-bold">Mã tin đăng</label>
									<div class="form-control form-control__id disabled" v-if="$route.name === 'warehouse.create'"><p class="mb-0" ></p></div>
									<div class="form-control form-control__id disabled" v-if="$route.name === 'warehouse.edit'"><p class="mb-0" style="color: #FAA831 " >{{form.migrate_status}}_{{form.id}}</p></div>
								</div> -->
								<InputCategory
									v-model="form.asset_type_id"
									vid="type_id"
									label="Loại tài sản"
									rules="required"
									class="col-12 col-lg-4 form-group-container"
									:disabled="true"
									:options="optionsType"
									@change="assetType($event)"
								/>
								<InputCategory
									v-model="form.transaction_type_id"
									vid="info"
									label="Loại giao dịch"
									rules="required"
									class="col-12 col-lg-4 form-group-container"
									:options="optionsTransactionType"
									@change="change_data_log('Loại giao dịch')"
								/>
								<InputDatePicker
									v-model="form.public_date"
									vid="public_date"
									rules="required"
									label="Thời điểm giao dịch"
									type="date"
									class="col-12 col-lg-4 form-group-container"
									@change="change_data_log('Thời điểm giao dịch')"
								/>
								<InputCategory
									v-model="form.source_id"
									vid="source_id"
									rules="required"
									label="Nguồn thông tin"
									class="col-12 col-lg-4 form-group-container"
									:options="optionsInfo"
									@change="change_data_log('Nguồn thông tin')"
								/>
								<InputText
									v-model="form.contact_person"
									vid="contact"
									label="Người liên hệ"
									class="col-12 col-lg-4 form-group-container"
									@change="change_data_log('Người liên hệ')"
								/>
								<InputText
									v-model="form.contact_phone"
									label="Số điện thoại"
									:max-length="11"
									class="col-12 col-lg-4 form-group-container"
									@change="change_data_log('Số điện thoại')"
								/>

								<div v-if="form.asset_type_id && form.asset_type_id === 39" style="padding:unset; margin:unset" class="w-100 row">
									<InputCategory
										v-model="form.project_id"
										vid="project_id"
										rules="required"
										label="Tên chung cư"
										class="col-12 col-lg-4 form-group-container"
										:options="optionsProjects"
										@change="handleChangeProject"
									/>
									<InputCategory
										v-model="form.block_id"
										vid="block_id"
										rules="required"
										label="Block (khu)"
										class="col-12 col-lg-4 form-group-container"
										:options="optionsBlocks"
										@change="handleChangeBlock"
									/>
									<InputCategory
										v-model="form.floor_id"
										vid="floor_id"
										rules="required"
										label="Tầng"
										class="col-12 col-lg-4 form-group-container"
										:options="optionsFloors"
										@change="handleChangeFloor"
									/>
									<InputText
										v-model="form.full_address"
										vid="full_address"
										:rules="'required'"
										label="Địa chỉ"
										class="col-12 form-group-container"
										@change="change_data_log('Địa chỉ')"
									/>
								</div>
								<InputCurrency
									v-model="form.total_amount"
									vid="total_amount"
									label="Giá (VND)"
									rules="required"
									@change="changeTotalAmount($event)"
									class="col-12 col-lg-4 form-group-container"
								/>
								<InputCategory
									v-if="form.room_details && form.room_details.length > 0"
									v-model="form.room_details[0].legal_id"
									vid="legal_id"
									rules="required"
									label="Loại pháp lý"
									class="col-12 col-lg-4 form-group-container"
									:options="optionsLegal"
									@change="change_data_log('Loại pháp lý')"
								/>
							</div>
							<div v-if="form.asset_type_id && form.asset_type_id !== 39" class="card-info">
								<h4 class="title" v-if="form.asset_type_id !== 39">Vị trí tài sản:</h4>
								<div class="row justify-content-between" v-if="form.asset_type_id !== 39 ">
									<InputCategory
										v-model="form.province_id"
										:disabled="'id' in $route.query && $route.name === 'warehouse.edit'"
										vid="province_id"
										label="Tỉnh/Thành"
										rules="required"
										class="col-12 col-md-4 col-lg-4 form-group-container"
										:options="optionsProvince"
										@change="changeProvince($event)"
									/>
									<InputCategory
										v-model="form.district_id"
										vid="district_id"
										label="Quận/Huyện"
										rules="required"
										class="col-12 col-md-4 col-lg-4 form-group-container"
										:options="optionsDistrict"
										@change="changeDistrict($event)"
									/>
									<InputCategory
										v-model="form.ward_id"
										vid="ward_id"
										label="Phường/Xã"
										rules="required"
										class="col-12 col-md-4 col-lg-4 form-group-container"
										:options="optionsWard"
										@change="changeWardStreet($event)"
									/>
								</div>
								<div class="row justify-content-between" v-if="form.asset_type_id !== 39">
									<InputCategory
										v-model="form.street_id"
										vid="street_id"
										label="Đường"
										rules="required"
										class="col-12 col-md-6 col-lg-4 form-group-container"
										:options="optionsStreet"
										@change="changeStreet($event)"
									/>
									<InputCategory
										v-model="form.distance_id"
										vid="distance_id"
										label="Đoạn"
										class="col-12 col-md-6 col-lg-4 form-group-container"
										:options="optionsDistance"
										@change="changeStreetDistance($event)"
									/>
									<div class="col-12 col-md-12 col-lg-4 form-group-container position-relative">
										<InputText
											v-model="form.coordinates"
											vid="coordinates"
											rules="required"
											label="Tọa độ"
											class="coordinates"
											:disabled-input="true"
											@change="change_data_log('Tọa độ')"
										/>
										<!-- <div v-if="$route.name === 'warehouse.create'" class="img-locate"> -->
										<div class="img-locate">
											<img src="@/assets/icons/ic_edit_2.svg" alt="locate" @click="handleOpenModalMap()">
										</div>
									</div>
								</div>
								<div class="row justify-content-between" v-if="form.asset_type_id !== 39">
									<!-- <InputNumberFormat
										v-model="form.land_no"
										vid="land_no"
										label="Thửa đất số"
										:max="999999"
										:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
										@change="changeLandNo($event)"
										:min="0"
										class="col-12 col-md-4 col-lg-4 form-group-container"
									/>
									<InputNumberFormat
										v-model="form.doc_no"
										vid="doc_no"
										label="Bản đồ số"
										:max="999999"
										:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
										@change="changeDocNo($event)"
										:min="0"
										class="col-12 col-md-4 col-lg-4 form-group-container"
									/> -->
								</div>
								<ApartmentInfo
									v-if="this.form.asset_type_id === 39"
									:room_details="form.room_details[0]"
									:apartment="form"/>
								<div class="row">
									<InputText
										v-model="form.full_address"
										vid="full_address"
										:rules="this.form.asset_type_id !== 39 ? 'required' : ''"
										label="Địa chỉ đầy đủ"
										class="col form-group-container"
										@change="change_data_log('Địa chỉ đầy đủ')"
									/>
									<InputCategory
										v-model="form.topographic"
										vid="topographic_id"
										label="Địa hình"
										rules="required"
										class="col-12 col-md-4 col-lg-4 form-group-container"
										:options="optionsTopographic"
										@change="change_data_log('Địa hình')"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<BlockInfo
					v-if="this.form.asset_type_id === 39"
					:basic_utilities="basic_utilities"
					:apartment_specification="form.apartment_specification"
				/>
				<ApartmentInfoDetail
					:key="key_render_apartment"
					v-if="form.asset_type_id === 39"
					:room_details="form.room_details[0]"
					:apartment_specification="form.apartment_specification"
					:apartment_id="form.apartment_id"
					:apartments="apartments"
					:directions="directions"
					:loai_can_ho="loai_can_ho"
					:furniture_list="furniture_list"
					@changeAparment="changeAparment"
					@changeWCNum="changeWCNum"
					@changeBedroomNum="changeBedroomNum"
					@changeAreaApartment="changeAreaApartment"
				/>
				<div class="card" v-if="this.form.asset_type_id === 37 || this.form.asset_type_id === 38">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">Thông tin thửa đất</h3>
							<img class="img-dropdown" :class="!showTable ? 'img-dropdown__hide' : ''" src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showTable = !showTable">
						</div>
					</div>
					<div class="card-body card-info card-land" v-if="showTable">
						<div class="contain-table">
							<table class="table-property" >
								<thead v-if="form.properties.length > 0">
									<tr>
										<th>Mã số</th>
										<th>Số tờ</th>
										<th>Số thửa</th>
										<th>Tọa độ</th>
										<th>Chiều rộng (m)</th>
										<th>Chiều dài (m)</th>
										<th>Diện tích (m<sup>2</sup>)</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
										<tr v-for="(property, index) in form.properties" :key="property.id" v-if="form.properties.length > 0">
											<td>
												<div class="d-flex justify-content-center align-items-center contain-total contain-total__table" v-if="$route.name === 'warehouse.edit'">
													<div class="num num-id d-flex justify-content-center text-center" @click="handleProperty(property, index)"><a>TSD_{{property.id}}</a></div>
												</div>
												<div class="d-flex align-items-center  contain-total contain-total__table" v-if="$route.name === 'warehouse.create'">
													<div class="num num-id d-flex justify-content-center text-center" @click="handleProperty(property, index)">Chỉnh sửa</div>
												</div>
											</td>
											<td>

												<div class="d-flex align-items-center contain-total contain-total__table">
													<div class="num"><p>{{property.compare_property_doc[0] !== undefined && property.compare_property_doc[0] !== null ? property.compare_property_doc[0].doc_num : ''}}</p></div>
												</div>
											</td>
											<td>
												<div class="d-flex align-items-center contain-total contain-total__table">
													<div class="num"><p>{{property.compare_property_doc[0] !== undefined && property.compare_property_doc[0] !== null ? property.compare_property_doc[0].plot_num : ''}}</p></div>
												</div>
											</td>
											<td>
												<div class="d-flex align-items-center coordinate">
													<div class="num"><p>{{property.coordinates}}</p></div>
												</div>
											</td>
											<td>
												<div class="d-flex align-items-center contain-total contain-total__table">
													<div class="num"><p>{{ formatNumber(property.front_side_width) }}</p></div>
												</div>
											</td>
											<td>
												<div class="d-flex align-items-center contain-total contain-total__table">
													<div class="num"><p>{{ formatNumber(property.insight_width) }}</p></div>
												</div>
											</td>
											<td>
												<div class="d-flex align-items-center contain-total contain-total__table">
													<div class="num"><p>{{ formatNumber(property.asset_general_land_sum_area) }}</p></div>
												</div>
											</td>
											<td>
												<div class="btn-delete" @click="removeTableRow(index)">
													<img src="@/assets/icons/ic_delete.svg" alt="delete">
												</div>
											</td>
										</tr>
								</tbody>
							</table>
						</div>

						<div v-if="form.properties.length < 1" class="btn-property">
							<div>
								<button class="btn btn-white btn-orange btn-add" type="button" @click.prevent="handleOpenModal()">
									<img src="@/assets/icons/ic_add-white.svg" alt="add">
									Thêm
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card" v-if="this.form.asset_type_id === 38">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">Công trình xây dựng</h3>
							<img class="img-dropdown" :class="!showLand? 'img-dropdown__hide' : ''" src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showLand = !showLand">
						</div>
					</div>
					<div class="card-body card-info card-land" v-if="showLand">
						<div class="contain-table contain-table__tangible">
							<table class="table-property">
								<thead v-if="form.tangible_assets.length > 0">
								<tr>
									<th>Mã số</th>
									<th>Loại</th>
									<th>Cấp nhà</th>
									<th>Chất lượng còn lại</th>
									<th>Diện tích sàn (m <sup>2</sup>)</th>
									<th>Đơn giá xây dựng (VND)</th>
									<th>Giá trị ước tính (VND)</th>
									<th>Hình ảnh</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="(tangible, index) in form.tangible_assets" :key="'tangible'+index">
									<td>
										<div class="d-flex justify-content-center align-items-center contain-total contain-total__table" v-if="$route.name === 'warehouse.edit'">
											<div class="num num-id d-flex justify-content-center text-center" @click="openModalTangible(tangible, index)"><a>TSN_{{tangible.id}}</a></div>
										</div>
										<div class="d-flex justify-content-center align-items-center contain-total contain-total__table" v-if="$route.name === 'warehouse.create'">
											<div class="num num-id text-nowrap d-flex justify-content-center text-center" @click="openModalTangible(tangible, index)"><a>Chỉnh sửa</a></div>
										</div>
									</td>
									<td>
										<InputCategory
											v-model="tangible.building_type_id"
											vid="building_type_id"
											label="Loại"
											disabled
											rules="required"
											class="contain-input contain-input__info contain-input__property"
											:options="optionsHousingType"
											@change="change_data_log('Loại CTXD')"
										/>
									</td>
									<td>
										<InputCategory
											v-model="tangible.building_category_id"
											vid="building_category_id"
											label="Cấp nhà"
											disabled
											class="contain-input contain-input__info contain-input__property"
											:options="optionsHousing"
											@change="change_data_log('Cấp nhà')"
										/>
									</td>
									<td>
										<InputPercent
											v-model="tangible.remaining_quality"
											disabled
											class="contain-input contain-input__info contain-input__property"
											@change="change_data_log('chất lượng còn lại')"
										/>
									</td>
									<td>
										<div class="d-flex align-items-center contain-total contain-total__table">
											<div class="num"><p>{{formatNumber(tangible.total_construction_base)}}</p></div>
										</div>
									</td>
									<td>
										<div class="d-flex align-items-center contain-total contain-total__table">
											<div class="num"><p>{{formatNumber(tangible.unit_price_m2)}}</p></div>
										</div>
									</td>
									<td>
										<div class="d-flex align-items-center contain-total contain-total__table">
											<div class="num" @change="changeAmount($event, index)"><p>{{formatNumber(tangible.estimation_value)}}</p></div>
										</div>
									</td>
									<td>
										<div class="contain-file">
											<form enctype="multipart/form-data" class="contain-file">
												<div v-if="tangible.pic.length === 0">
													Không
												</div>
												<!--                        <p v-if="tangible.image !== null">{{tangible.file}}</p>-->
												<div class="img-contain img-contain__table" v-for="images in tangible.pic" :key="images.id">
													<div class="contain-img contain-img__table" @click="openModalImage(images)">
														<img class="img img-table" :src="images.link" alt="img">
													</div>
												</div>
											</form>
										</div>
									</td>
									<td>
										<div class="btn-delete" @click="removeTangible(index)">
											<img src="@/assets/icons/ic_delete.svg" alt="delete">
										</div>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
						<div v-if="form.tangible_assets.length < 1" class="btn-property">
							<button class="btn btn-white btn-orange btn-add" type="button" @click.prevent="handleTangible">
								<img src="@/assets/icons/ic_add-white.svg" alt="add">
								Thêm
							</button>
						</div>
					</div>
				</div>

				<div class="card card__order" v-if="this.form.asset_type_id === 38 || this.form.asset_type_id === 37">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">Tài sản khác</h3>
							<img class="img-dropdown" :class="!showOther ? 'img-dropdown__hide' : ''" src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showOther = !showOther">
						</div>
					</div>
					<div class="card-body card-info card-land" v-if="showOther">
						<div class="contain-table">
							<table class="table-property table-property__order">
								<thead>
								<tr v-if="form.other_assets.length > 0 ">
									<th>Loại tài sản</th>
									<th>Giá trị (VND)</th>
									<th>Tải hình ảnh</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
										<tr v-for="(other, index) in form.other_assets" :key="other.id">
										<td>

											<InputText
												v-model="other.other_asset"
												label="Loại tài sản"
												:vid="'typePropertyOther' + index"
												class="contain-input contain-input__info contain-input__property contain-input__order"
												:max-length="200"
												rules="required"
												@change="change_data_log('Loại tài sản khác')"
											/>
										</td>
										<td>
											<InputCurrency
												v-model="other.total_amount"
												:vid="'total_amount_other'+ index"
												label="Giá trị"
												rules="required"
												@change="changeAmountOther($event, index)"
												class="contain-input contain-input__info contain-input__property contain-input__order"/>
										</td>
										<td>
											<div class="contain-file justify-content-center">
												<form enctype="multipart/form-data" class="contain-file">
													<div class="img-upload ml-0" v-if="other.pic.length === 0">
														Tải ảnh lên
														<input type="file" accept="image/png, image/jpeg, image/gif, image/jpg" @change="onImageOtherChange($event, index)" />
													</div>
													<div class="img-contain img-contain__table" v-for="images in other.pic" :key="images.id">
														<div class="contain-img contain-img__table" @click="openModalImage(images)">
															<img class="img img-table" :src="images.link" alt="img">
														</div>
													</div>
												</form>
											</div>
										</td>
										<td>
											<div class="btn-delete" @click="removeOther(index)">
												<img src="@/assets/icons/ic_delete.svg" alt="delete">
											</div>
										</td>
									</tr>

								</tbody>
							</table>
						</div>
						<div class="btn-property">
							<button class="btn btn-white btn-orange btn-add" type="button" @click="addOtherAsset">
								<img src="@/assets/icons/ic_add-white.svg" alt="add">
								Thêm
							</button>
						</div>
					</div>
				</div>
				<div class="card">
						<div class="card-title card-title__img">
							<form enctype="multipart/form-data" class="d-flex align-items-center">
								<h3 class="title">Hình ảnh</h3>
								<div class="img-upload">
									<img src="@/assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
									Tải ảnh lên
									<input type="file" ref="file" id="image" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImagePropertyChange($event)"/>
								</div>
							</form>
						</div>
						<div class="card-body">
							<div class="container-img row mr-0 ml-0" >
								<div class="img-empty text-center" v-if="form.pic.length === 0">
									<img src="@/assets/images/img_emply.svg" alt="empty">
									<p class="empty-content">Chưa có hình</p>
								</div>
								<div class="contain-img col-4 col-lg-2 contain-img__property" v-for="(images, index) in form.pic" :key="images.id">
									<div class="delete" @click="removeImage(index)">X</div>
									<img class="img" :src="images.link" alt="img" @click="openModalImage(images)">
								</div>
							</div>
						</div>
				</div>
<!--        Phần này sẽ thêm-->
				<div class="card" v-if="this.form.asset_type_id !== ''">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">Ước tính đơn giá {{this.form.asset_type_id === 39 ? 'căn hộ' : 'đất'}} </h3>
							<img class="img-dropdown" :class="!showInfoTransaction ? 'img-dropdown__hide': ''" src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfoTransaction = !showInfoTransaction">
						</div>
					</div>
					<div class="card-body card-info" v-if="showInfoTransaction">
						<div class="container-fluid">
							<div class="card-info">
								<div  class="row justify-content-between">
									<div class="col-12 col-md-4 col-lg-3 form-group-container">
										<label class="color-black font-weight-bold">Tổng giá trị (VND)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.total_amount)}}</p></div>
									</div>
									<div v-if="this.form.asset_type_id === 39" class="col-12 col-md-4 col-lg-3 form-group-container">
										<label class="color-black font-weight-bold">Diện tích (m²)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{form.room_details && form.room_details[0].area ? formatNumber( form.room_details[0].area): 0}}</p><p class="mb-0 ml-2">m<sup>2</sup></p></div>
									</div>
									<div class="col-12 col-md-4 col-lg-4 form-group-container"/>
								</div>
								<div class="row justify-content-between">
									<div class="col-12 col-md-4 col-lg-3 form-group-container">
										<label class="color-black font-weight-bold">Tổng giá trị {{this.form.asset_type_id === 39? 'căn hộ' : 'tài sản'}} sau điều chỉnh (VND)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.total_estimate_amount)}}</p></div>
									</div>
									<div class="col-12 col-md-4 col-lg-3">
										<InputPercentNegative
										:key="render_percent"
											v-model="form.adjust_percent"
											vid="adjust_percent"
											label="Tỉ lệ điều chỉnh"
											rules="required"
											@change="changeAdjustPercent($event)"
											class="form-group-container"
										/>
										<!-- <span class="text-error" v-if="form.adjust_percent > 20 || form.adjust_percent < -20">Tỉ lệ điều chỉnh không được nhỏ hơn -20% và không được lớn hơn 20%</span> -->
									</div>
									<div class="col-12 col-md-4 col-lg-4 form-group-container">
										<!-- <label class="color-black font-weight-bold">Giá trị giảm/tăng (VND)</label> -->
										<InputNumberNegative
										:key="render_percent"
											v-model="form.adjust_amount"
											vid="adjust_amount"
											label="Giá trị giảm/tăng (VND)"
											rules="required"
											@change="changeAdjustAmount($event)"
											class="form-group-container"
										/>
										<!-- <div class="form-control d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.adjust_amount)}}</p></div> -->
									</div>
								</div>
								<div class="row justify-content-between" v-if="this.form.asset_type_id === 39">
									<div class="col-12 col-md-4 col-lg-3 form-group-container">
										<label class="color-black font-weight-bold">Đơn giá bình quân căn hộ (đ/m<sup>2</sup>)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(this.form.room_details[0].unit_price)}}</p></div>
									</div>
								</div>
								<div class="row justify-content-between" v-if="this.form.asset_type_id !== 39">
									<div class="col-12 col-md-4 col-lg-3 form-group-container">
										<label class="color-black font-weight-bold">Tổng giá trị đất thuần còn lại (VND)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.total_raw_amount)}}</p></div>
										<span class="text-error" v-if="form.total_raw_amount < 0">Tổng giá trị đất thuần còn lại không được nhỏ hơn 0</span>
									</div>
									<div class="col-12 col-md-4 col-lg-3 form-group-container">
										<label class="color-black font-weight-bold">Tổng giá trị công trình xây dựng (VND)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.total_construction_amount)}}</p></div>
									</div>
									<div class="col-12 col-md-4 col-lg-4 form-group-container">
										<label class="color-black font-weight-bold">Tổng giá trị tài sản khác (VND)</label>
										<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.total_order_amount)}}</p></div>
									</div>
								</div>
								<div v-if="this.form.asset_type_id !== 39">
									<div v-if="this.form.convert_fee_total > 0">
										<hr>
										<h4 class="title">Chi phí chuyển đổi mục đích sử dụng đất</h4>
										<div class="row justify-content-between">
											<div class="col-12 form-group-container">
												<label class="color-black font-weight-bold">Tổng giá trị chuyển mục đích sử dụng đất (VND)</label>
												<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.convert_fee_total)}}</p></div>
											</div>
										</div>
										<h3 class="title">Trong đó:</h3>
										<div class="row justify-content-between" v-for="(property,index) in form.properties" v-bind:key="index">
											<div class="col-12 col-md-6 form-group-container" v-for="property_detail in property.property_detail" :key="property_detail.id" v-if="property_detail.convert_fee > 0">
												<label class="color-black font-weight-bold">Chi phí chuyển mục đích sử dụng từ {{ property_detail.land_type_purpose_data !== undefined && property_detail.land_type_purpose_data !== null ? property_detail.land_type_purpose_data.description : ''}} sang {{form.max_value_description}} (VND)</label>
												<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(property_detail.convert_fee)}}</p></div>
											</div>
										</div>
									</div>
									<div v-if="this.form.total_amount > 0">
										<hr>
										<h4 class="title">Giá trị QSDĐ và đơn giá đất chi tiết</h4>
										<div class="row justify-content-between">
											<div class="col-12 form-group-container">
												<label class="color-black font-weight-bold">Giá trị QSDĐ ước tính (VND)</label>
												<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(form.total_land_unit_price)}}</p></div>
												<span class="text-error" v-if="form.total_land_unit_price && form.total_land_unit_price < 0">Giá trị QSDĐ ước tính không được nhỏ hơn 0</span>
											</div>
										</div>
										<div v-if="sortedArray.length > 0">
											<h3 class="title">Trong đó:</h3>
											<ul class="row justify-content-between">
												<li class="col-12 col-md-6 form-group-container"  v-for="purpose_use_land in sortedArray" :key="purpose_use_land.id" >
													<label class="color-black font-weight-bold">Đơn giá đất {{purpose_use_land.land_type_purpose_data !== undefined && purpose_use_land.land_type_purpose_data !== null ? purpose_use_land.land_type_purpose_data.description : ''}} (VND)</label>
													<div class="form-control disabled d-flex justify-content-end"><p class="mb-0">{{formatCurrency(purpose_use_land.price_land)}}</p></div>
													<span class="text-error" v-if="purpose_use_land.price_land <= 0">Loại đất {{purpose_use_land.land_type_purpose_data !== undefined && purpose_use_land.land_type_purpose_data !== null ? purpose_use_land.land_type_purpose_data.description : ''}} hiện có đơn giá bằng 0. Vui lòng kiểm tra lại giá trị đầu vào.</span>
												</li>
											</ul>
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
							<h3 class="title">Ghi chú </h3>
							<img class="img-dropdown" :class="!showNote ? 'img-dropdown__hide': ''" src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showNote = !showNote">
						</div>
					</div>
					<div class="card-body card-info" v-if="showNote">
						<div class="container-fluid">
							<div class="row">
								<InputTextarea
									label=""
									v-model="form.note"
									vid="note"
									:rows="10"
									class="col-12 form-group-container label-none"
									@change="change_data_log('Ghi chú')"
								/>
							</div>
						</div>
					</div>
				</div>
				<ModalPropertyCreate
					v-if="openModal"
					@cancel="cancelProperty"
					:info="this.form"
					:property_index="this.property_index"
					:property="this.property"
					:unit_price="this.unit_price"
					:street="this.street"
					:frontSideOptions="frontSideOptions"
					:twoSidesLandOptions="twoSidesLandOptions"
					:individualRoadOptions="individualRoadOptions"
					@action="handleSave"
				/>
				<ModalTangible
					v-if="openTangible"
					:info="this.form"
					v-bind:compare_properties="this.compare_assets"
					v-bind:tangible ="this.tangible"
					v-bind:tangible_index = "this.tangible_index"
					@cancel="cancelTangible"
					@action="handleSaveTangible"
				/>
				<ModalCancel
					v-if="openModalCancel"
					@cancel="openModalCancel = false"
					@action="handleCancel"
				/>
				<ModalWarning
					v-if="openModalWarning"
					@cancel="openModalWarning = false"
					@action="handleActionWarning"
				/>
				<ModalMap
					v-if="openModalMap"
					@cancel="openModalMap = false"
					:location="this.location"
					:address="this.form.full_address"
					:center_map="this.form.coordinates"
					@action="handleCoordinates"
				/>
				<ModalImage
					v-if="openImage"
					v-bind:image_detail ="this.image_detail.link"
					@cancel="openImage = false"
				/>
				<ModalNotification
					v-if="openNotification"
					v-bind:notification="this.message"
					@cancel="openNotification = false"
					@action= "handleHome"
				/>
				<div class="loading" :class="{'loading__true': isSubmit}">
					<a-spin />
				</div>
			</div>
			<div v-if="!isMobile()" class="btn-footer d-md-flex d-block justify-content-end align-items-center">
				<div class="d-lg-flex d-block button-contain">
					<button class="btn btn-white btn-orange text-nowrap" :class="{'btn_loading disabled': isSubmit}" type="submit" > <img src="@/assets/icons/ic_save.svg" :class="{'d-none': isSubmit}"  style="margin-right: 12px" alt="save"> Lưu</button>
					<button @click.prevent="handleOpenModalCancel" class="btn btn-white text-nowrap" :class="{'disabled': isSubmit}">
						<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="save">
						Hủy
					</button>
				</div>
			</div>
			<div v-else class="btn-footer d-md-flex d-block" style="bottom: 60px;">
				<div class="d-lg-flex d-block button-contain row" style="justify-content: space-around;display: flex!important;">
					<button class="btn btn-white btn-orange text-nowrap col-6" style="width: unset;margin: 0;padding: 0;" :class="{'btn_loading disabled': isSubmit}" type="submit" > <img src="@/assets/icons/ic_save.svg" :class="{'d-none': isSubmit}"  style="margin-right: 12px" alt="save"> Lưu</button>
					<button @click.prevent="handleOpenModalCancel" class="btn btn-white text-nowrap col-6" style="width: unset;margin: 0;padding: 0;" :class="{'disabled': isSubmit}">
						<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="save">
						Hủy
					</button>
				</div>
			</div>
		</ValidationObserver>
	</div>
</template>
<script>
import Vue from 'vue'
import VueNumeric from 'vue-numeric'
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputAreaCustom from '@/components/Form/InputAreaCustom'
import InputLengthArea from '@/components/Form/InputLengthArea'
import InputPercent from '@/components/Form/InputPercent'
import InputPercentNegative from '@/components/Form/InputPercentNegative'
import InputSwitch from '@/components/Form/InputSwitch'
import ModalPropertyCreate from '@/components/Modal/ModalPropertyCreate'
import ModalCancel from '@/components/Modal/ModalCancel'
import ModalWarning from '@/components/Modal/ModalWarning'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputTextarea from '@/components/Form/InputTextarea'
import InputCurrency from '@/components/Form/InputCurrency'
import InputArea from '@/components/Form/InputArea'
import WareHouse from '@/models/WareHouse'
// import ModalMap from '@/components/Modal/ModalMap'
import ModalMap from '../certification_asset/real_estate/component/modals/ModalMap'
import ModalTangible from '@/components/Modal/ModalTangible'
import ModalNotification from '@/components/Modal/ModalNotification'
import ModalImage from '@/components/Modal/ModalImage'
import File from '@/models/File'
import {STATUS} from '@/enum/status.enum'
import ApartmentInfo from '@/pages/warehouse/components/ApartmentInfo'
import BlockInfo from '@/pages/warehouse/components/BlockInfo'
import ApartmentInfoDetail from '@/pages/warehouse/components/ApartmentInfoDetail'
import store from '@/store'
import * as types from '@/store/mutation-types'
import InputNumberNegative from '@/components/Form/InputNumberNegative'
Vue.use(VueNumeric)
export default {
	name: '',
	components: {
		ModalImage,
		ModalMap,
		InputText,
		InputNumberFormat,
		InputPercent,
		InputPercentNegative,
		InputLengthArea,
		InputAreaCustom,
		InputCategory,
		InputSwitch,
		ModalPropertyCreate,
		ModalNotification,
		ModalTangible,
		ModalWarning,
		InputDatePicker,
		ModalCancel,
		ApartmentInfo,
		BlockInfo,
		ApartmentInfoDetail,
		InputTextarea,
		InputCurrency,
		InputArea,
		InputNumberNegative
	},
	data () {
		return {
			render_percent: 989898,
			key_render_apartment: 2323221,
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
			street: '',
			property_index: '',
			property: '',
			location: {
				lng: '',
				lat: ''
			},
			idData: '',
			image: '',
			price: '',
			unit_price: {},
			openModalWarning: false,
			openNotification: false,
			openTangible: false,
			showAsset: true,
			showInfo: true,
			showTable: true,
			showLand: true,
			showOther: true,
			showNote: true,
			openModal: false,
			openEdit: false,
			openImage: false,
			openError: false,
			showInfoTransaction: true,
			openModalCancel: false,
			openModalMap: false,
			purpose: '',
			center: {lat: 10.964112, lng: 106.856461},
			markers: [],
			purpose_use_lands: [],
			compare_assets: [],
			landProperty: [],
			orderAssets: [],
			building_categories: [],
			compare_properties: [],
			topographics: [],
			counter: 0,
			counterOther: 0,
			show: false,
			types: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			landTypes: [],
			propertyTypes: [],
			housingTypes: [],
			infoSources: [],
			transactionType: [],
			type_purposes: [],
			landUser: [],
			legalTypes: [],
			projects: [],
			blocks: [],
			floors: [],
			apartments: [],
			basic_utilities: [],
			directions: [],
			furniture_list: [],
			loai_can_ho: [],
			unit: '',
			isSubmit: false,
			isEditTangible: false,
			isEditProperty: false,
			file: null,
			count: [],
			max: 0,
			min: 0,
			form: {
				note: '',
				project_id: '',
				block_id: '',
				floor_id: '',
				apartment_id: '',
				openModalWarning: '',
				max_value_description: '',
				convert_fee_total: 0,
				created_by: '',
				adjust_percent: 0,
				adjust_amount: 0,
				id_amount: '',
				status: 1,
				migrate_status: 'TSS',
				input_source: 'DONAVA',
				post_id: '',
				asset_type_id: '',
				legal_id: '',
				province_id: '',
				district_id: '',
				doc_num: '',
				ward_id: '',
				street_id: '',
				distance_id: '',
				topographic: 93,
				transaction_type_id: '',
				land_no: '',
				landType: '',
				doc_no: '',
				full_address: '',
				coordinates: '',
				source_id: '',
				public_date: '',
				convert_fee: '',
				property_other: '',
				contact_phone: '',
				total_area: 0,
				total_construction_area: 0,
				total_construction_amount: 0,
				total_order_amount: 0,
				total_other_amount: 0,
				total_raw_amount: 0,
				total_amount: '',
				total_area_amount: 0,
				total_estimate_amount: 0,
				average_land_unit_price: 0,
				total_land_unit_price: 0,
				contact_person: '',
				properties: [],
				tangible_assets: [],
				apartment_specification: {
					block_list_id: '',
					handover_year: '',
					total_floor: '',
					basement_floor: '',
					commercial_floor: '',
					living_floor: '',
					lift_number: '',
					other_utilities: '',
					utilities: []
				},
				room_details: [
					{
						legal_id: '',
						block_list_id: '',
						parent_id: '',
						room_num: '',
						floor: '',
						area: '',
						bedroom_num: '',
						wc_num: '',
						direction_id: '',
						furniture_quality_id: '',
						start_using_year: '',
						unit_price: '',
						description: ''
					}
				],
				other_assets: [],
				unit_prices: [],
				pic: []
			},
			data_change: []
		}
	},
	created () {
		if ('id' in this.$route.query && this.$route.name === 'warehouse.edit') {
			this.form = Object.assign(this.form, { ...this.$route.meta['detail'] })
			if (this.form.province === null || this.form.province === undefined) {
				this.form.province_id = null
			}
			if (this.form.district === null || this.form.district === undefined) {
				this.form.dictrict_id = null
			}
			if (this.form.ward === null || this.form.ward === undefined) {
				this.form.ward_id = null
			}
			if (this.form.street === null || this.form.street === undefined) {
				this.form.street_id = null
			}
			if (this.form.distance === null || this.form.distance === undefined) {
				this.form.distance_id = null
			}
		} else if (this.$route.name === 'warehouse.create') {
			this.form.asset_type_id = +this.$route.query.asset_type_id
			this.assetType()
		}
	},
	mounted () {
		this.image = process.env.API_URL
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
				key: 'detail'
			}
		},
		optionsInfo () {
			return {
				data: this.infoSources,
				id: 'id',
				key: 'description'
			}
		},
		optionsTransactionType () {
			return {
				data: this.transactionType,
				id: 'id',
				key: 'description'
			}
		},
		optionsHousingType () {
			return {
				data: this.housingTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsHousing () {
			return {
				data: this.building_categories,
				id: 'id',
				key: 'description'
			}
		},
		optionsLandType () {
			return {
				data: this.landTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsTopographic () {
			return {
				data: this.topographics,
				id: 'id',
				key: 'description'
			}
		},
		optionsLegal () {
			return {
				data: this.legalTypes,
				id: 'id',
				key: 'description'
			}
		},
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
		}
	},
	methods: {
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		},
		change_data_log (value) {
			if (this.$route.name === 'warehouse.edit') {
				if (!this.data_change.includes(value)) {
					this.data_change.push(value)
				}
				// console.log('các trường bị thay đổi', this.data_change)
			}
		},
		getCenter (center) {
			this.center = center
		},
		openModalImage (data) {
			this.openImage = true
			this.image_detail = data
		},
		openModalTangible (data, index) {
			this.change_data_log('Công trình xây dựng')
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openTangible = true
			this.isEditTangible = true
			if (data.individual_road === 1) {
				data.individual_road_switch = true
			} else {
				data.individual_road_switch = false
			}
			if (data.front_side === 1) {
				data.front_side_switch = true
			} else {
				data.front_side_switch = false
			}
			this.tangible = data
			this.tangible_index = index
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
		retype () {
			this.$router.push({name: 'warehouse.create'})
		},
		onImageChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			this.file = e.target.files[0]
			if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
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
		},
		createImage () {
			let reader = new FileReader()
			let v = this.form.tangible_assets
			v.forEach(item => {
				reader.onload = (e) => {
					item.image = e.target.result
					item.file = this.file.name
				}
			})
			reader.readAsDataURL(this.file)
		},
		uploadImage () {
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				let v = this.form.tangible_assets
				if (response.data.data) {
					v.forEach(item => {
						const pic = {
							link: response.data.data.link,
							picture_type: response.data.data.picture_type
						}
						item.pic.push(pic)
					})
				} else if (response.data.error) {
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		onImageOtherChange (e, index) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			this.file = e.target.files[0]
			if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
				this.createImageOther(index)
				this.uploadImageOther(index)
			} else {
				this.$toast.open({
					message: 'Hình không đúng định dạng vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		createImageOther (index) {
			let reader = new FileReader()
			reader.onload = (e) => {
				this.form.other_assets[index].image = e.target.result
				this.form.other_assets[index].file = this.file.name
			}
			reader.readAsDataURL(this.file)
		},
		uploadImageOther (index) {
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				// let v = this.form.other_assets
				if (response.data.data) {
					const pic = {
						link: response.data.data.link,
						picture_type: response.data.data.picture_type
					}
					this.form.other_assets[index].pic.push(pic)
				} else if (response.data.error) {
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		onImagePropertyChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
					this.createImageProperty()
					this.uploadImageProperty()
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
		createImageProperty () {
			let reader = new FileReader()
			let v = this
			reader.onload = (e) => {
				v.image_property = e.target.result
			}
			reader.readAsDataURL(this.file)
		},
		uploadImageProperty () {
			this.isSubmit = true
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					const item = {
						link: response.data.data.link,
						picture_type: response.data.data.picture_type
					}
					this.form.pic.push(item)
					this.isSubmit = false
				} else if (response.data.error) {
					this.isSubmit = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		assetType (event) {
			this.showAsset = this.form.asset_type_id === 38
			if (this.form.asset_type_id === 39) {
				this.form.province_id = ''
				this.form.district_id = ''
				this.form.ward_id = ''
				this.form.street_id = ''
				this.form.coordinates = ''
				this.form.distance_id = ''
				this.form.full_address = ''
				this.form.topographic = ''
				this.form.doc_no = ''
				this.form.land_no = ''
				this.form.properties = []
				this.form.tangible_assets = []
				this.form.other_assets = []
				if (this.form.room_details.length === 0) {
					this.form.room_details.push({
						legal_id: '',
						block_list_id: '',
						parent_id: '',
						room_num: '',
						floor: '',
						area: '',
						bedroom_num: '',
						wc_num: '',
						direction_id: '',
						furniture_quality_id: '',
						start_using_year: '',
						unit_price: '',
						description: ''
					})
				}
				if (!this.form.apartment_specification) {
					this.form.apartment_specification = {
						block_list_id: '',
						handover_year: '',
						total_floor: '',
						basement_floor: '',
						commercial_floor: '',
						living_floor: '',
						lift_number: '',
						other_utilities: '',
						utilities: []
					}
				}
			} else if (this.form.asset_type_id === 37) {
				this.form.apartment_id = ''
				this.form.tangible_assets = []
				this.form.other_assets = []
				this.form.apartment_specification = {}
				this.form.room_details = []
			} else if (this.form.asset_type_id === 38) {
				this.form.apartment_id = ''
				this.form.apartment_specification = {}
				this.form.room_details = []
			}
			this.change_data_log('Loại tài sản')
		},
		async handleProperty (data, index) {
			this.change_data_log('Thông tin thửa đất')
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.isEditProperty = true
			this.property = data
			this.property_index = index
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
			await this.getAddress()
		},
		async handleOpenModal () {
			this.isEditProperty = false
			this.property_index = ''
			this.property = ''
			this.frontSideOptions.items.preSelected = ''
			this.twoSidesLandOptions.items.preSelected = ''
			this.individualRoadOptions.items.preSelected = ''
			await this.getAddress()
		},
		handleActionWarning () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openModal = true
		},
		async handleOpenModalMap () {
			this.change_data_log('Tọa độ')
			this.openModalMap = true
		},
		handleTangible () {
			this.isEditTangible = false
			this.openTangible = true
			this.tangible = ''
			this.tangible_index = ''
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalCancel () {
			this.openModalCancel = true
		},
		addOtherAsset () {
			this.counterOther++
			this.form.other_assets.push({
				other_asset: '',
				total_amount: 0,
				file: null,
				image: null,
				pic: []
			})
		},
		removeTableRow (index) {
			const type_purposes = this.type_purposes
			let land_use = []
			let purpose_max = ''
			let max_value = 0
			let total_area = 0
			let total_area_amount = 0
			let convert_fee = 0
			this.form.properties.splice(index, 1)
			this.form.properties.forEach(item => {
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				total_area_amount = total_area_amount + item.asset_general_value_sum_area
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			})
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
					purpose_max = land.land_type_purpose
				}
			})
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description
				}
			})
			this.form.total_area = total_area
			this.form.total_area_amount = total_area_amount
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					property_detail.convert_fee = parseInt((max_value - property_detail.circular_unit_price) * property_detail.total_area)
					convert_fee = convert_fee + property_detail.convert_fee
					type_purposes.forEach(purposes => {
						if (property_detail.land_type_purpose === purposes.id) {
							property_detail.land_type_purpose_data.description = purposes.description
						}
					})
				})
			}
			)
			this.form.convert_fee_total = convert_fee
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			this.form.properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					if (((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price)) > 0) {
						property_detail.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price))
					} else {
						property_detail.price_land = 0
					}
				})
			}
			)
			this.sortArrayPropertyDetail()
		},
		removeTangible (index) {
			const property = this.form.properties
			const type_purposes = this.type_purposes
			let total_area = 0
			let land_use = []
			let purpose_max = ''
			let max_value = 0
			property.forEach(item => {
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
					purpose_max = land.land_type_purpose
				}
			})
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description
				}
			})
			this.form.tangible_assets.splice(index, 1)
			const total = this.form.tangible_assets
			let totalConstructionAmount = 0
			let totalConstructionArea = 0
			total.forEach(item => {
				totalConstructionAmount = totalConstructionAmount + parseInt(item.estimation_value)
				totalConstructionArea = totalConstructionArea + parseInt(item.total_construction_area)
			}
			)
			this.form.total_construction_area = totalConstructionArea
			this.form.total_construction_amount = totalConstructionAmount
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			property.forEach(item => {
				item.property_detail.forEach(unit => {
					if (((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price)) > 0) {
						unit.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price))
					} else {
						unit.price_land = 0
					}
				})
			}
			)
			this.sortArrayPropertyDetail()
		},
		removeOther (index) {
			const property = this.form.properties
			const type_purposes = this.type_purposes
			let total_area = 0
			let land_use = []
			let purpose_max = ''
			let max_value = 0
			property.forEach(item => {
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
					purpose_max = land.land_type_purpose
				}
			})
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description
				}
			})
			this.form.other_assets.splice(index, 1)
			this.form.total_other_amount = this.amountOther()
			this.form.total_order_amount = this.amountOther()
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			property.forEach(item => {
				item.property_detail.forEach(unit => {
					if (((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price)) > 0) {
						unit.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price))
					} else {
						unit.price_land = 0
					}
				})
			}
			)
			this.sortArrayPropertyDetail()
		},
		removeImage (index) {
			this.form.pic.splice(index, 1)
			document.getElementById('image').value = ''
		},
		changeLandNo (event) {
			if (event !== undefined && event !== null) {
				this.form.land_no = parseFloat(event).toFixed(0)
			} else {
				this.form.land_no = 0
			}
		},
		changeDocNo (event) {
			if (event !== undefined && event !== null) {
				this.form.doc_no = parseFloat(event).toFixed(0)
			} else {
				this.form.doc_no = 0
			}
		},
		changeProvince (provinceId) {
			this.change_data_log('Tỉnh/Thành')
			this.districts = []
			this.wards = []
			this.streets = []
			this.form.district_id = ''
			this.form.ward_id = ''
			this.form.street_id = ''
			this.getDistrictsByProvinceId(+provinceId)
			this.getWardsByDistrictId(+provinceId)
			this.getStreetByDistrictId(+provinceId)
			const data = this.form
			let provinceName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
				}
			})
			this.form.full_address = provinceName
		},
		changeDistrict (districtId) {
			this.change_data_log('Quận/Huyện')
			this.wards = []
			this.streets = []
			this.form.ward_id = ''
			this.form.street_id = ''
			const data = this.form
			let provinceName = ''
			let districtName = ''
			this.getWardsByDistrictId(+districtId)
			this.getStreetByDistrictId(+districtId)
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
						}
					})
				}
			})
			this.form.full_address = districtName + ',' + provinceName
		},
		changeStreetDistance () {
			this.change_data_log('Đoạn')
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			let streetTemp = this.titleCase(streetName)
			if (wardName === '') {
				this.form.full_address = streetTemp + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.form.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.form.full_address = streetTemp + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		changeWardStreet () {
			this.change_data_log('Phường/Xã')
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			let streetTemp = this.titleCase(streetName)
			if (wardName === '') {
				this.form.full_address = streetTemp + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.form.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.form.full_address = streetTemp + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		changeStreet (streetId) {
			this.change_data_log('Đường')
			const data = this.form
			this.form.distance_id = ''
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.getDistanceByStreetId(+streetId)
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			if (streetName) {
				this.street = streetName
			} else {
				this.street = ''
			}
			let streetTemp = this.titleCase(streetName)
			if (wardName === '') {
				this.form.full_address = streetTemp + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.form.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.form.full_address = streetTemp + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
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
		findStreet () {
			let streetName = ''
			const data = this.form
			this.streets.forEach(street => {
				if (street.id === data.street_id) {
					streetName = street.name
				}
			})
			let streetTemp = this.titleCase(streetName)
			this.street = streetTemp
		},
		getAddress () {
			const data = this.form

			if (data.coordinates === '') {
				this.$toast.open({
					message: 'Vui lòng chọn tọa độ',
					type: 'error',
					position: 'top-right'
				})
				return
			}
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			let distanceName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
				}
			})
			this.districts.forEach(district => {
				if (district.id === data.district_id) {
					districtName = district.name
				}
			})
			this.wards.forEach(ward => {
				if (ward.id === data.ward_id) {
					wardName = ward.name
				}
			})
			this.streets.forEach(street => {
				if (street.id === data.street_id) {
					streetName = street.name
				}
			})
			this.distances.forEach(distance => {
				if (distance.id === data.distance_id) {
					distanceName = distance.name
				}
			})
			this.unit_price = {
				province: provinceName,
				district: districtName,
				ward: wardName,
				street: streetName,
				distance: distanceName
			}
			if (provinceName !== '' && districtName !== '' && wardName !== '' && streetName !== '') {
				document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
				this.openModal = true
			} else {
				this.openModalWarning = true
			}
		},
		totalConstructionBase (event, index) {
			for (let i = 0; i < this.form.tangible_assets.length; i++) {
				if (i === index) {
					this.form.tangible_assets[i].total_construction_base = +event
				}
			}
			this.form.tangible_assets[index].estimation_value = this.form.tangible_assets[index].total_construction_base * this.form.tangible_assets[index].unit_price_m2 * (this.form.tangible_assets[index].remaining_quality / 100)
		},
		changeArea (event, index) {
			for (let i = 0; i < this.form.tangible_assets.length; i++) {
				if (i === index) {
					this.form.tangible_assets[i].total_construction_area = +event
				}
			}
			const total = this.form.tangible_assets
			let totalArea = 0
			total.forEach(item => {
				totalArea = totalArea + parseInt(item.total_construction_area)
			}
			)
			if (totalArea !== '') {
				this.form.total_construction_area = totalArea
			} else {
				this.form.total_construction_area = 0
			}
		},
		amount () {
			const total = this.form.tangible_assets
			let totalConstructionAmount = 0
			total.forEach(item => {
				totalConstructionAmount = totalConstructionAmount + parseInt(item.estimation_value)
			}
			)
			return totalConstructionAmount
		},
		changeAmount (event, index) {
			for (let i = 0; i < this.form.tangible_assets.length; i++) {
				if (i === index) {
					this.form.tangible_assets[i].estimation_value = +event
				}
			}
			this.form.total_construction_amount = this.amount()
			if (this.form.total_area_amount !== 0 || this.form.total_other_amount !== 0) {
				this.form.total_amount = this.amount() + this.form.total_area_amount + this.form.total_other_amount
			} else {
				this.form.total_amount = this.amount()
			}
		},
		amountOther () {
			const total = this.form.other_assets
			let totalAmount = 0
			total.forEach(item => {
				totalAmount = totalAmount + parseInt(item.total_amount)
			})
			return totalAmount
		},
		changeAmountOther (event, index) {
			this.change_data_log('Giá trị tài sản khác')
			const property = this.form.properties
			const type_purposes = this.type_purposes
			let total_area = 0
			let land_use = []
			let purpose_max = ''
			let max_value = 0
			for (let i = 0; i < this.form.other_assets.length; i++) {
				if (i === index) {
					this.form.other_assets[i].total_amount = +event
				}
				this.form.total_order_amount = this.amountOther()
				this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			}
			property.forEach(item => {
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
					purpose_max = land.land_type_purpose
				}
			})
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description
				}
			})
			this.form.total_other_amount = this.amountOther()
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			property.forEach(item => {
				item.property_detail.forEach(unit => {
					if (((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price)) > 0) {
						unit.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price))
					} else {
						unit.price_land = 0
					}
				})
			}
			)
			this.sortArrayPropertyDetail()
		},
		checkTransactionType () {
			let asset_type = ''
			this.propertyTypes.forEach(propertyType => {
				if (propertyType.id === this.form.asset_type_id) {
					asset_type = propertyType.description
				}
			})
			return asset_type
		},

		async validateBeforeSubmit () {
			let set = false
			for (let i = 0; i < this.form.other_assets.length; i++) {
				let other = this.form.other_assets[i]
				if (other.total_amount <= 0) {
					set = true
				}
			} if (set == true) {
				this.$toast.open({
					message: 'Vui lòng nhập giá trị tài sản khác lơn hơn 0',
					type: 'error',
					position: 'top-right'
				})
				return
			}

			const isValid = await this.$refs.observer.validate()
			if (!isValid) {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			} else if ((this.form.asset_type_id === 37 || this.form.asset_type_id === 38) && this.form.properties.length === 0) {
				this.$toast.open({
					message: 'Vui lòng nhập thông tin thửa đất',
					type: 'error',
					position: 'top-right'
				})
			} else if ((this.form.asset_type_id === 37 || this.form.asset_type_id === 38) && this.form.properties.length === 0) {
				this.$toast.open({
					message: 'Vui lòng nhập thông tin thửa đất',
					type: 'error',
					position: 'top-right'
				})
			} else if ((this.form.total_land_unit_price < 0 || this.form.total_raw_amount < 0) && (this.form.asset_type_id === 37 || this.form.asset_type_id === 38)) {
				this.$toast.open({
					message: 'Giá trị tài sản không được nhỏ hơn 0',
					type: 'error',
					position: 'top-right'
				})
			// } else if (this.form.adjust_percent > 20 || this.form.adjust_percent < -20) {
			// 	this.$toast.open({
			// 		message: 'Tỉ lệ điều chỉnh không được nhỏ hơn -20% và không được lớn hơn 20%',
			// 		type: 'error',
			// 		position: 'top-right'
			// 	})
			} else if (this.sortedArray.length > 0 && this.sortedArray.find(array => array.price_land <= 0)) {
				this.$toast.open({
					message: 'Hiện có đơn giá đất bằng 0. Vui lòng kiểm tra lại giá trị đầu vào',
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.form.status = STATUS.ACTIVE
				this.handleSubmit()
			}
		},
		async handleSubmit () {
			if (this.isSubmit == true) {
				this.$toast.open({
					message: 'Hệ thống đang xử lý, vui lòng đợi trong giây lát.',
					type: 'warning',
					position: 'top-right'
				})
				return
			} else {
				this.isSubmit = true
			}
			let data = this.form
			// console.log('data', data)
			if (this.$route.name === 'warehouse.edit') {
				data['data_change'] = this.data_change
				await this.updateDictionary(data)
			} else {
				await this.createDictionary(data)
			}
		},
		async createDictionary (data) {
			try {
				const resp = await WareHouse.create(data)
				if (resp) {
					if (resp.data) {
						this.isSubmit = false
						this.message = 'Tạo mới mã tài sản so sánh ' + '<b>' + 'TSS_' + resp.data + '</b>' + ' thành công.'
						this.openNotification = true
					} else if (resp.error) {
						this.$toast.open({
							message: resp.error.message,
							type: 'error',
							position: 'top-right',
							duration: 3000
						})
						this.isSubmit = false
					}
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async updateDictionary (data) {
			try {
				const resp = new WareHouse(data)
				await resp.save()
				if (resp.data) {
					this.isSubmit = false
					this.message = 'Chỉnh sửa mã tài sản so sánh ' + '<b>' + 'TSS_' + resp.data + '</b>' + ' thành công.'
					this.openNotification = true
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
					this.isSubmit = false
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async handleHome () {
			if (this.$route.name === 'warehouse.create') {
				this.$router.push({name: 'warehouse.index'}).catch(_ => {
				})
			} else if (this.$route.name === 'warehouse.edit') {
				this.$router.go(-1)
			}
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.form.created_by = profile.data.user.id
		},
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		async getDictionary () {
			try {
				let res = this.$store.getters.dictionaries
				if (res && res.length === 0) {
					res = await WareHouse.getDictionaries()
					store.commit(types.SET_DICTIONARIES, {...res})
				}
				const resp = await WareHouse.getInterior()
				this.furniture_list = [...resp.data]
				this.building_categories = [...res.data.cap_nha]
				this.housingTypes = [...res.data.loai_nha]
				this.infoSources = [...res.data.nguon_thong_tin]
				this.legalTypes = [...res.data.phap_ly]
				let basic_utilities = [...res.data.tien_ich_co_ban]
				this.basic_utilities = basic_utilities.filter(item => item.acronym !== null)
				this.directions = [...res.data.huong_can_ho]
				this.loai_can_ho = [...res.data.loai_can_ho]
				let propertyTypesTem = [...res.data.loai_tai_san]
				let propertyTypesBDS = []
				propertyTypesBDS = await propertyTypesTem.filter(item => item.dictionary_acronym === 'BDS')
				this.propertyTypes = propertyTypesBDS
				this.transactionType = [...res.data.loai_giao_dich]
				this.type_purposes = [...res.data.loai_dat_chi_tiet]
				this.landTypes = [...res.data.loai_dat]
				this.topographics = [...res.data.dia_hinh]
				this.furniture_list.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.legalTypes.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.housingTypes.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.building_categories.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.infoSources.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.basic_utilities.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.directions.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.propertyTypes.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.type_purposes.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.landTypes.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.transactionType.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
				this.topographics.forEach(item => {
					item.description = this.formatSentenceCase(item.description)
				})
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getProjects () {
			this.projects = this.$store.getters.apartments
			if (this.projects && this.projects.length === 0) {
				const res = await WareHouse.getProjects()
				if (res.data) {
					this.projects = res.data
					store.commit(types.SET_APARTMENT, [...res.data])
				}
			}
			if (this.form.project_id) {
				this.getBlocks(this.form.project_id)
			}
		},
		getBlocks (id) {
			let project = this.projects.filter(item => item.id === id)
			this.blocks = project[0].block
			if (this.form.block_id) {
				this.getFloors(this.form.block_id)
			}
		},
		getFloors (id) {
			let block = this.blocks.filter(item => item.id === id)
			this.floors = block[0].floor
		},
		// async getApartments (id) {
		// 	const res = await WareHouse.getApartmentFloor(id)
		// 	if (res.data) {
		// 		this.apartments = [...res.data]
		// 	}
		// },
		handleChangeProject (projectId) {
			this.change_data_log('Tên chung cư')
			this.blocks = []
			this.floors = []
			this.apartments = []
			this.form.block_id = ''
			this.form.floor_id = ''
			this.form.apartment_id = ''
			if (projectId) {
				let project = this.projects.filter(item => item.id === projectId)
				this.form.coordinates = project[0].coordinates
				if (project[0].utilities) {
					this.form.apartment_specification.utilities = project[0].utilities
				} else {
					this.form.apartment_specification.utilities = []
				}
				this.form.province_id = project[0].province_id
				this.form.district_id = project[0].district_id
				this.form.ward_id = project[0].ward_id
				this.form.street_id = project[0].street_id
				let provinceName = ''
				let districtName = ''
				let wardName = ''
				if (project[0].province) {
					provinceName = project[0].province.name
				}
				if (project[0].district) {
					districtName = project[0].district.name
				}
				if (project[0].ward) {
					wardName = project[0].ward.name
				}
				this.form.full_address = `${wardName}, ` + `${districtName}, ` + provinceName
				this.getBlocks(+projectId)
			}
		},
		handleChangeBlock (blockId) {
			this.change_data_log('Block (khu)')
			this.floors = []
			this.apartments = []
			this.form.floor_id = ''
			this.form.apartment_id = ''
			if (blockId) {
				let block = this.blocks.filter(item => item.id === blockId)
				this.form.apartment_specification.handover_year = block[0].handover_year
				this.getFloors(blockId)
			}
		},
		handleChangeFloor (floorId) {
			this.change_data_log('Tầng')
			// if (floorId) {
			// 	this.getApartments(floorId)
			// }
			// this.apartments = []
			// this.form.apartment_id = ''
			// this.key_render_apartment += 1
		},
		changeAparment (event) {
			this.form.apartment_id = event
		},
		changeAreaApartment (event) {
			this.form.room_details[0].area = event
			if (this.form.total_amount) {
				this.form.total_estimate_amount = parseInt(this.form.total_amount) + parseInt(this.form.adjust_amount)
			} else {
				this.form.total_estimate_amount = 0
			}
			if (this.form.room_details.length > 0 && this.form.room_details[0].area > 0) {
				this.form.room_details[0].unit_price = parseFloat(this.form.total_estimate_amount / this.form.room_details[0].area).toFixed(0)
				this.form.average_land_unit_price = parseFloat(this.form.total_estimate_amount / this.form.room_details[0].area).toFixed(0)
			}
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			const total = this.form.properties
			let total_area = 0
			let total_area_amount = 0
			let land_use = []
			let max_value = 0
			let min_value = 9999999999999999
			if (total && total.length > 0) {}
			total.forEach(item => {
				total_area_amount = total_area_amount + parseFloat(item.asset_general_value_sum_area)
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			this.landUser = land_use
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
				}
				if (land.circular_unit_price < min_value) {
					min_value = land.circular_unit_price
				}
			})
			total.forEach(item => {
				item.property_detail.forEach(property_detail => {
					property_detail.convert_fee = parseInt((max_value - property_detail.circular_unit_price) * property_detail.total_area)
					if ((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price) > 0) {
						property_detail.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price))
					} else {
						property_detail.price_land = 0
					}
				})
			}
			)
			if (this.form.asset_type_id !== 39) {
				this.sortArrayPropertyDetail()
			}
		},
		changeBedroomNum (event) {
			this.form.room_details[0].bedroom_num = event
		},
		changeWCNum (event) {
			this.form.room_details[0].wc_num = event
		},
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				if (this.form.province_id === '') {
					this.form.province_id = 34
					const data = this.form
					this.provinces.forEach(province => {
						if (province.id === data.province_id) {
							this.form.full_address = province.name
						}
					})
				}
				if (this.form.province_id !== '' && this.form.province_id !== undefined && this.form.province_id !== null) {
					await this.getDistrictsByProvinceId(this.form.province_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async getDistrictsByProvinceId (id) {
			try {
				const resp = await WareHouse.getDistrict(id)
				this.districts = [...resp.data]
				if (this.form.district_id !== '' && this.form.district_id !== undefined && this.form.district_id !== null) {
					await this.getWardsByDistrictId(this.form.district_id)
					await this.getStreetByDistrictId(this.form.district_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getWardsByDistrictId (id) {
			try {
				const resp = await WareHouse.getWard(id)
				this.wards = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getStreetByDistrictId (id) {
			try {
				const resp = await WareHouse.getStreet(id)
				this.streets = [...resp.data]
				if (this.form.street_id !== '' && this.form.street_id !== undefined && this.form.street_id !== null) {
					await this.findStreet()
					await this.getDistanceByStreetId(this.form.street_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDistanceByStreetId (id) {
			const resp = await WareHouse.getDistance(id)
			this.distances = [...resp.data]
		},
		onCancel () {
			return this.$router.push({name: 'warehouse.index'})
		},
		async handleCancel () {
			this.isSubmit = true
			if (this.$route.name === 'warehouse.create') {
				return this.$router.push({name: 'warehouse.index'})
			} else if (this.$route.name === 'warehouse.edit') {
				this.$router.go(-1)
			}
		},
		async handleSave (property, property_index) {
			if (this.isEditProperty === false) {
				this.form.properties.push(property)
			} else {
				this.form.properties[property_index] = property
			}
			const properties = this.form.properties
			let total_area = 0
			let total_area_amount = 0
			let purpose_use_lands = []
			let max_value = 0
			let min_value = 9999999999999999
			let type_purposes = this.type_purposes
			let purpose_min = ''
			let purpose_max = ''
			let convert_fee = 0
			properties.forEach(property => {
				total_area_amount = total_area_amount + parseFloat(property.asset_general_value_sum_area)
				total_area = total_area + parseFloat(property.asset_general_land_sum_area)
				property.property_detail.forEach(property_detail => {
					purpose_use_lands.push(property_detail)
				})
			}
			)
			this.landUser = purpose_use_lands
			purpose_use_lands.forEach(purpose_of_using_land => {
				if (purpose_of_using_land.circular_unit_price > max_value) {
					max_value = purpose_of_using_land.circular_unit_price
					purpose_max = purpose_of_using_land.land_type_purpose
				}
				if (purpose_of_using_land.circular_unit_price < min_value) {
					min_value = purpose_of_using_land.circular_unit_price
					purpose_min = purpose_of_using_land.land_type_purpose
				}
			})
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_min) {
					this.purpose = purposes.description
				}
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description
				}
			})
			this.max = max_value
			this.min = min_value
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)

			this.form.total_area_amount = total_area_amount
			this.form.total_area = total_area
			properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					property_detail.convert_fee = 0
					if (purpose_use_lands.length > 1) {
						property_detail.convert_fee = parseInt((max_value - property_detail.circular_unit_price) * property_detail.total_area)
						convert_fee = convert_fee + property_detail.convert_fee
						type_purposes.forEach(type_purpose => {
							if (property_detail.land_type_purpose === type_purpose.id) {
								property_detail.land_type_purpose_data.description = type_purpose.description
							}
						})
					}
				})
				this.form.convert_fee_total = convert_fee
			}
			)
			this.form.total_land_unit_price = this.form.total_raw_amount + convert_fee
			properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					if (((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price)) > 0) {
						property_detail.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price))
					} else {
						property_detail.price_land = 0
					}
				})
			}
			)
			this.sortArrayPropertyDetail()
			this.compareAsset()
		},
		cancelProperty () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openModal = false
		},
		compareAsset () {
			const properties = this.form.properties
			let compare_assets = []
			this.compare_assets = []
			properties.forEach(property => {
				property.compare_property_doc.forEach(compare_property_doc => {
					compare_assets.push({
						id: compare_property_doc.plot_num,
						plot_num: 'Số tờ: ' + compare_property_doc.doc_num + ', Số thửa: ' + compare_property_doc.plot_num
					})
					// if (compare_assets.length > 0) {
					//   compare_assets.forEach(compare_asset => {
					//     if ((compare_property_doc.doc_num === compare_asset.doc_num && compare_property_doc.plot_num !== compare_asset.plot_num) || (compare_property_doc.doc_num !== compare_asset.doc_num)) {
					//       compare_assets.push(compare_property_doc)
					//     } else {}
					//   })
					// } else if (compare_assets.length === 0) {
					//   compare_assets.push(compare_property_doc)
					// }
				})
			}
			)
			this.compare_assets = compare_assets
		},
		sortArrayPropertyDetail () {
			let purpose_use_lands = []
			this.purpose_use_lands = []
			this.form.average_land_unit_price = 0
			this.form.properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					purpose_use_lands.push(property_detail)
				})
				this.purpose_use_lands = purpose_use_lands
				this.averageLandUnitPrice()
			})
		},
		averageLandUnitPrice () {
			let average_land_unit_price = 0
			this.purpose_use_lands.forEach(purpose_use_land => {
				average_land_unit_price = (average_land_unit_price + purpose_use_land.price_land)
			})
			this.form.average_land_unit_price = parseFloat(average_land_unit_price / this.purpose_use_lands.length).toFixed(0)
		},
		async handleSaveTangible (tangible, tangible_index) {
			if (this.isEditTangible === false) {
				this.form.tangible_assets.push(tangible)
			} else {
				this.form.tangible_assets[tangible_index] = tangible
			}
			const total = this.form.tangible_assets
			const property = this.form.properties
			let land_use = []
			let total_tangible_area = 0
			let total_tangible_amount = 0
			let max_value = 0
			let total_area = 0
			let purpose_max = ''
			let type_purposes = this.type_purposes
			total.forEach(item => {
				total_tangible_area = total_tangible_area + parseFloat(item.total_construction_area)
				total_tangible_amount = total_tangible_amount + parseInt(item.estimation_value)
			})
			property.forEach(item => {
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
					purpose_max = land.land_type_purpose
				}
			})
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description
				}
			})
			this.form.total_construction_area = total_tangible_area
			this.form.total_construction_amount = total_tangible_amount
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			property.forEach(item => {
				item.property_detail.forEach(unit => {
					if (((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price)) > 0) {
						unit.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price))
					} else {
						unit.price_land = 0
					}
				})
			}
			)
			this.sortArrayPropertyDetail()
		},
		cancelTangible () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openTangible = false
		},
		async handleCategory (index) {
			const resp = await WareHouse.getBuildingPrices(this.form.tangible_assets[index].building_category_id)
			this.form.tangible_assets[index].unit_price_m2 = parseInt(resp.data)
			this.form.tangible_assets[index].estimation_value = this.form.tangible_assets[index].total_construction_base * this.form.tangible_assets[index].unit_price_m2 * (this.form.tangible_assets[index].remaining_quality / 100)
		},
		changeTotalAmount (event) {
			this.change_data_log('Giá (VND)')
			this.form.total_amount = event
			this.form.adjust_amount = parseFloat(this.form.total_amount * (this.form.adjust_percent / 100)).toFixed(0)
			this.render_percent++
			console.log('this.form.adjust_amount', this.form.adjust_amount)
			if (this.form.total_amount) {
				this.form.total_estimate_amount = parseInt(this.form.total_amount) + parseInt(this.form.adjust_amount)
			} else {
				this.form.total_estimate_amount = 0
			}
			if (this.form.room_details.length > 0 && this.form.room_details[0].area > 0) {
				this.form.room_details[0].unit_price = parseFloat(this.form.total_estimate_amount / this.form.room_details[0].area).toFixed(0)
				this.form.average_land_unit_price = parseFloat(this.form.total_estimate_amount / this.form.room_details[0].area).toFixed(0)
			}
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			const total = this.form.properties
			let total_area = 0
			let total_area_amount = 0
			let land_use = []
			let max_value = 0
			let min_value = 9999999999999999
			if (total && total.length > 0) {}
			total.forEach(item => {
				total_area_amount = total_area_amount + parseFloat(item.asset_general_value_sum_area)
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			this.landUser = land_use
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
				}
				if (land.circular_unit_price < min_value) {
					min_value = land.circular_unit_price
				}
			})
			total.forEach(item => {
				item.property_detail.forEach(property_detail => {
					property_detail.convert_fee = parseInt((max_value - property_detail.circular_unit_price) * property_detail.total_area)
					if ((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price) > 0) {
						property_detail.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - property_detail.circular_unit_price))
					} else {
						property_detail.price_land = 0
					}
				})
			}
			)
			if (this.form.asset_type_id !== 39) {
				this.sortArrayPropertyDetail()
			}
		},
		changeAdjustAmount (event) {
			console.log('đổi tiền')
			if (event) {
				this.form.adjust_amount = event
				this.form.adjust_percent = event / this.form.total_amount * 100
				this.render_percent++
			}
			this.changeAdjustPercent(this.form.adjust_percent)
		},
		changeAdjustPercent (event) {
			if (event !== undefined && event !== null) {
				this.form.adjust_percent = parseFloat(event).toFixed(2)
			} else {
				this.form.adjust_percent = 0
			}
			this.form.adjust_amount = parseFloat(this.form.total_amount * (event / 100)).toFixed(0)
			this.render_percent++
			if (this.form.total_amount !== null && this.form.total_amount !== undefined && this.form.total_amount !== '') {
				this.form.total_estimate_amount = parseInt(this.form.total_amount) + parseInt(this.form.adjust_amount)
			}
			if (this.form.room_details.length > 0 && this.form.room_details[0].area > 0) {
				this.form.room_details[0].unit_price = parseFloat(this.form.total_estimate_amount / this.form.room_details[0].area).toFixed(0)
				this.form.average_land_unit_price = parseFloat(this.form.total_estimate_amount / this.form.room_details[0].area).toFixed(0)
			}
			this.form.total_raw_amount = parseInt(this.form.total_estimate_amount) - parseInt(this.form.total_construction_amount) - parseInt(this.form.total_order_amount)
			this.form.total_land_unit_price = this.form.total_raw_amount + this.form.convert_fee_total
			const total = this.form.properties
			let total_area = 0
			let total_area_amount = 0
			let land_use = []
			let max_value = 0
			let min_value = 9999999999999999
			total.forEach(item => {
				total_area_amount = total_area_amount + parseFloat(item.asset_general_value_sum_area)
				total_area = total_area + parseFloat(item.asset_general_land_sum_area)
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail)
				})
			}
			)
			this.landUser = land_use
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price
				}
				if (land.circular_unit_price < min_value) {
					min_value = land.circular_unit_price
				}
			})
			total.forEach(item => {
				item.property_detail.forEach(unit => {
					unit.convert_fee = parseInt((max_value - unit.circular_unit_price) * unit.total_area)
					if ((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price) > 0) {
						unit.price_land = parseInt((this.form.total_land_unit_price / total_area) - (max_value - unit.circular_unit_price))
					} else {
						unit.price_land = 0
					}
				})
			}
			)
			if (this.form.asset_type_id !== 39) {
				this.sortArrayPropertyDetail()
			}
		},
		remainingQuality (event, index) {
			for (let i = 0; i < this.form.tangible_assets.length; i++) {
				if (i === index) {
					this.form.tangible_assets[i].remaining_quality = +event
				}
			}
			this.form.tangible_assets[index].estimation_value = this.form.tangible_assets[index].total_construction_base * this.form.tangible_assets[index].unit_price_m2 * (this.form.tangible_assets[index].remaining_quality / 100)
		},
		async handleCoordinates (coordinates) {
			this.form.coordinates = coordinates
			this.location.lat = coordinates.split(',')[0]
			this.location.lng = coordinates.split(',')[1]
		}

	},
	async beforeMount () {
		this.isSubmit = true
		await this.getDictionary()
		await this.getProfiles()
		if (!(this.$route.name === 'warehouse.detail')) {
			if (+this.form.asset_type_id !== 39) {
				this.sortArrayPropertyDetail()
				this.compareAsset()
				await this.getProvinces()
			} else {
				await this.getProjects()
			}
		}
		this.isSubmit = false
		this.form.source_id = (this.infoSources && this.infoSources.length > 0) ? this.infoSources[0].id : ''
		// this.getWardsByDistrictId()
		// this.getStreetByDistrictId()
	}
}
</script>
<style scoped lang="scss">
.card__order{
	max-width: 100%;
	.contain-table{
		overflow: hidden;
		@media (max-width: 1023px) {
			overflow: hidden;
		}
	}
	.contain-img {
		.img-table {
			min-width: 50px;
			min-height: 50px;
			width: 70px;
			height: 70px;
		}
	}
}
.title{
	margin-bottom: 0;
	font-size: 24px;
	font-weight: 700;
}
.btn{
	&-white{
		max-height: none;

		line-height: 19.07px;
		min-width: 153px;
		margin-right: 15px;
		&:last-child{
			margin-right: 0;
		}
	}
	&-orange {
		max-height: none;

		line-height: 19.07px;
		min-width: 153px;
		margin-right: 15px;
		color: #FFFFFF;
		background: #FAA831;
	}
	&-contain{
		margin-bottom: 55px;
		@media (max-width: 768px) {
			margin-bottom: 30px;
		}
	}
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
.contain-input{
	display: flex;
}

.input{
	&-grid{
		margin-top: 28px;
		display: grid;
		grid-template-columns: 1fr 1fr 506px;
		grid-column-gap: 8%;
		grid-row-gap: 25px;
		@media (max-width: 1680px) {
			grid-template-columns: 1fr 1fr 1.5fr;
		}
		@media (max-width: 768px) {
			grid-template-columns: 1fr ;
		}
	}
}
.img-locate{
	cursor: pointer;
	position: absolute;
	right: 14px;
	top: 2.1rem;
	background: #FFFFFF;
	height: 2.1rem;
	width: 32px;
	display: grid;
	place-items: center;
	img {
		height: 60%;
	}
}
.img-dropdown{
	cursor: pointer;
	width: 18px;
	&__hide{
		transform: rotate(90deg);
		transition: .3s;
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
			@media (max-width: 787px) {
				padding: 12px;
			}
		}
	}
	tbody{
		td{
			border: 1px solid #E5E5E5;
			&:first-child{
				border-left: none;
				border-right: none;
			}
			padding: 20px 14px;
		}
	}
	&__order {
		tbody{
			td{
				&:first-child{
					width: 40%;
				}
				&:last-child{
					width: 70px;
				}
				padding: 20px 70px;
				@media (max-width: 1023px) {
					padding: 20px 30px;
				}
			}
		}
	}
}
.btn-property{
	padding: 10px;
}
.btn-delete{
	display: flex;
	align-items: center;
	cursor: pointer;
	background: #FFFFFF;
	border: 0.777778px solid #000000;
	border-radius: 5.88235px;
	padding: 10px;
	margin: auto;
	width: 36px;
	height: 36px;
	img{
		width: 100%;
		height: auto;
	}

}
.contain-table{
	&__tangible{
		overflow-y: hidden;
		overflow-x: auto;
	}
	@media (max-width: 1440px) {
		overflow-y: hidden;
		overflow-x: auto;
	}
	.table-property{
		width: 100%;
	}
}
.contain-file{
	display: flex;
	align-items: center;
	h3{
		margin-top: 8px;
		margin-bottom: 0;
	}
}
.btn-upload{
	background: #FFFFFF;
	white-space: nowrap;
	border: 1px solid #555555;
	box-sizing: border-box;
	border-radius: 5px;
	padding: 5px 19px;
	font-size: 10px;
}
.img-upload{
	margin-left: 20px;
	position: relative;
	width: 123px;
	height: 35px;
	color: #fff;
	background: #FAA831;
	font-size: 1rem;
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	display: flex;
	justify-content: center;
	align-items: center;
	box-sizing: border-box;
	cursor: pointer;
	input{
		cursor: pointer !important;
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		width: 100%;
		opacity: 0;
	}
}
.contain-img{
	aspect-ratio:1/1;
	overflow: hidden;
	height: auto;
	position: relative;
	text-align: center;
	margin-bottom: 10px;
	.img{
		object-fit: cover;
		width: 100%;
		height: 100%;
		cursor: pointer;
		&-table{
			margin: auto;
			min-width: 50px;
			min-height: 50px;
			width: 50px;
			height: 50px;
			object-fit: cover;
		}
	}
	&__table{
		width: auto;
		margin-bottom: 0;
	}
	.delete{
		position: absolute;
		top: 0;
		right: 0.75rem;
		background: #000000;
		color: #FFFFFF;
		width: 20px;
		height: 20px;
		text-align: center;
		line-height: 1.5;
		cursor: pointer;
		font-weight: 700;
		border-radius: 5px;
	}
}
.btn-loading{
	color: #FFFFFF !important;
}
.contain-total{
	display: grid !important;
	margin-right: 0;
	grid-template-columns: 1fr 1fr;
	color: #333333;
	@media (max-width: 1440px) {
		display: block !important;
	}
		.num{
			padding: 0 11px 0 24px;
			height: 35px;
			line-height: 1.5;
			width: 180px;
			border-radius: 5px;
			border: 1px solid #555555;
			display: flex;
			align-items: center;
			justify-content: flex-end;
			background: #f1f1f1 !important;
			cursor: not-allowed;
			user-select: none;
			@media (max-width: 787px) {
				width: 100% !important;
			}
			p{
				margin-bottom: 0;
			}
			&-id{
				color: #FAA831;
				text-align: center !important;
				background: #FFFFFF !important;
				border: none;
				width: 100%;
				padding: 0;
			}
		}
		.name{
			margin-bottom: 0;
			font-size: 1.125rem;
			font-weight: 500;
			margin-right: 20px;
			@media (max-width: 1440px) {
				margin-bottom: 10px;
				font-weight: 700;
			}
		}
	&__last{
		.num{
			width: 315px;
			@media (max-width: 767px){
				width: calc(100vw - 120px) ;
			}
		}
	}
	&__table{
		grid-template-columns: 1fr;
		.num{
			text-align: left;
			margin: auto;
			&-id{
				cursor: pointer;
				color: #FAA831;
				text-align: center !important;
			}
		}
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
		background: rgba(255, 255, 255, 0.62);
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

.form-group-container {
	margin-top: 15px;
}
.contain-table {
	overflow: auto;
}
.percent{
	top: 0;
	right: 0;
	bottom: 0;
	position: absolute;
	background: #f1f1f1;
	border: 1px solid #000000;
	border-left: none;
	height: 100%;
	line-height: 1.5;
	border-bottom-right-radius: 5px;
	border-top-right-radius: 5px;
	padding: 5.5px;
	box-sizing: border-box;
}
.info-container{
	margin-bottom: 85px;
	@media (max-width: 767px) {
		margin-bottom: 145px;
	}
}
.coordinate{
	color: #000000;
	background: #f1f1f1;
	padding: 0 11px 0 24px;
	display: flex;
	align-items: center;
	height: 35px;
	border-radius: 5px;
	border: 1px solid #555555;
	.num{
		p{
			margin-bottom: 0;
		}
	}
}
hr{
	background: #0b0d10;
	opacity: .5;
 }
.done{
	text-align: center;
	font-size: 20px;
}
.color-black{
	color: #333333;
}
.container-img{
 padding: .75rem 0;
	border: 1px solid #0b0d10;
}
ul,li{
	list-style: none;
}
ul{
	padding-left: 0;
}
.text-error{
	color: #cd201f;
	font-size: 12px;
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
.price_unit {
	justify-content: right
}
.btn_loading {
		position: relative;
		color: white !important;
		text-shadow: none !important;
		pointer-events: none;
	}
.btn_loading:after {
	content: '';
	display: inline-block;
	vertical-align: text-bottom;
	border: 1px solid wheat;
	border-right-color: transparent;
	border-radius: 50%;
	color: #ffffff;
	position: absolute;
	width: 1rem;
	height: 1rem;
	left: calc(50% - .5rem);
	top: calc(50% - .5rem);
	-webkit-animation: spinner-border .75s linear infinite;
	animation: spinner-border .75s linear infinite;
}
</style>
