<template>
	<div
		class="modal-delete d-flex justify-content-center align-items-center"
		@click.self="handleCancel"
	>
		<div class="card">
			<div class="card-header d-flex justify-content-end align-items-center">
				<img
					@click="handleCancel"
					src="../../assets/icons/ic_cancel-1.svg"
					alt="icon"
				/>
			</div>
			<div class="card-body" id="printBody">
				<img style="padding: 0;" src="@/assets/images/header-kqsb.png" />
				<div
					class="text-right mt-3"
					style="color: #000; font-size: 12px !important;"
				>
					Mã sơ bộ:
					<span
						style="color: #000; font-size: 12px !important;"
						class="font-weight-bold"
						>{{ data.id ? "TSSB_" + data.id : "" }}</span
					>
				</div>

				<div class="w-100">
					<div class="card mb-1">
						<div class="card-header vendorListHeading mb-2">
							THÔNG TIN VỀ NGƯỜI YÊU CẦU
						</div>
						<div class="card-body">
							<div class="data-row">
								<div class="data-label">Tên người yêu cầu:</div>
								<div class="data-value">
									{{
										data.step_3.petitioner_name
											? data.step_3.petitioner_name.toUpperCase()
											: ""
									}}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Ngày yêu cầu:</div>
								<div class="data-value">
									{{ data.step_3.request_date }}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Mục đích thẩm định:</div>
								<div class="data-value">
									{{
										data.step_3.appraise_purpose
											? data.step_3.appraise_purpose.name
											: ""
									}}
								</div>
							</div>
						</div>
					</div>
					<div class="card mb-3">
						<div class="card-header vendorListHeading mb-2">
							THÔNG TIN SƠ BỘ VỀ TÀI SẢN
						</div>
						<div class="card-body">
							<div class="data-row">
								<div class="data-label">Loại tài sản:</div>
								<div class="data-value">
									{{
										data.step_1.general_infomation.asset_type
											? formatSentenceCase(
													data.step_1.general_infomation.asset_type.description
											  )
											: ""
									}}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Tên tài sản:</div>
								<div class="data-value">
									{{
										data.step_1.general_infomation.appraise_asset
											? data.step_1.general_infomation.appraise_asset
											: ""
									}}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Địa chỉ tài sản:</div>
								<div class="data-value">
									{{
										data.step_1.general_infomation.full_address_street
											? data.step_1.general_infomation.full_address_street
											: ""
									}}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Tọa độ:</div>
								<div class="data-value">
									{{
										data.step_1.general_infomation.coordinates
											? data.step_1.general_infomation.coordinates
											: ""
									}}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Mô tả vị trí:</div>
								<div class="data-value">
									{{
										!isApartment && data.step_1.traffic_infomation.description
											? data.step_1.traffic_infomation.description
											: data.step_1.apartment_properties.description
											? data.step_1.apartment_properties.description
											: ""
									}}
								</div>
							</div>
							<!-- <div class="data-row">
								<div class="data-label">Sơ đồ vị trí:</div>
							</div> -->
						</div>
					</div>
					<!-- <div class="main-map">
						<div class="layer-map">
							<l-map
								ref="lmap"
								:zoom="zoom"
								:center="[
									data.step_1.general_infomation.coordinates.split(',')[0],
									data.step_1.general_infomation.coordinates.split(',')[1]
								]"
								:options="{
									attributionControl: false,
									zoomControl: false,
									dragging: false,
									touchZoom: false,
									scrollWheelZoom: false,
									doubleClickZoom: false
								}"
								:maxZoom="20"
							>
								<l-tile-layer
									:url="url"
									:options="{ maxNativeZoom: 19, maxZoom: 20 }"
								></l-tile-layer>
								
								<l-marker
									:lat-lng="[
										data.step_1.general_infomation.coordinates.split(',')[0],
										data.step_1.general_infomation.coordinates.split(',')[1]
									]"
								>
									<l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
										<img
											style="width: 60px !important"
											class="icon_marker"
											src="@/assets/images/svg_home.svg"
											alt=""
										/>
									</l-icon>
								
								</l-marker>
							</l-map>
						</div>
					</div> -->
					<!-- <img src="@/assets/images/header-kqsb.png" class="mt-2" /> -->
					<div class="card-header vendorListHeading mt-3 mb-3">
						KẾT QUẢ ƯỚC TÍNH SƠ BỘ
					</div>
					<div
						class="card mb-4"
						v-if="!isApartment && data.assets && data.assets.length > 0"
					>
						<table cellspacing="0" cellpadding="0">
							<thead class="vendorListHeadingResult p-0">
								<tr>
									<th style="border-top-left-radius: 15px;" class="text-left">
										Quyền sử dụng đất
									</th>
									<!-- <th class="text-center">Loại đất</th> -->
									<th class="text-center">MĐSD</th>
									<th class="text-center">
										Diện tích (m<sup style="font-size: 9px !important">2</sup>)
									</th>
									<th class="text-center">Đơn giá (đ)</th>
									<th style="border-top-right-radius: 15px;" class="text-right">
										Thành tiền (đ)
									</th>
								</tr>
							</thead>
							<tbody class="vendorListHeadingResultBody">
								<tr
									v-for="(item, index) in data.assets"
									:key="'detail' + index"
								>
									<td class="text-left">{{ item.description }}</td>
									<td class="text-center">
										{{ item.land_type_description }}
									</td>
									<td class="text-center">
										{{ formatNumber(item.area) }}
										<!-- m<sup style="font-size: 11px"
											>2</sup
										> -->
									</td>
									<td class="text-center">{{ formatNumber(item.price) }}</td>
									<td class="text-right">{{ formatNumber(item.total) }}</td>
									<!-- <td class="text-center">{{ format(item.price) + " đ" }}</td>
									<td class="text-right">{{ format(item.total) + " đ" }}</td> -->
								</tr>
								<tr class="summary">
									<td
										style="border-bottom-left-radius: 15px;"
										class="text-left font-weight-bold"
									>
										TỔNG CỘNG:
									</td>
									<td class="text-right"></td>
									<td class="text-right"></td>
									<!-- <td class="text-right">
										{{ formatArea(data.land_area) }}m<sup
											style="font-size: 11px"
											>2</sup
										>
									</td> -->
									<td class="text-right"></td>
									<!-- <td class="text-right">
										{{ format(data.land_total) + " đ" }}
									</td> -->
									<td
										style="border-bottom-right-radius: 15px;"
										class="text-right font-weight-bold"
									>
										{{ formatNumber(data.totalLandPrice) }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div
						class="card mb-4"
						v-if="
							!isApartment &&
								data.step_3.tangible_assets &&
								data.step_3.tangible_assets.length > 0
						"
					>
						<table>
							<thead class="vendorListHeadingResult p-0">
								<tr>
									<th style="border-top-left-radius: 15px;" class="text-left">
										Công trình xây dựng
									</th>
									<th class="text-center">
										Diện tích sàn (m<sup style="font-size: 9px !important"
											>2</sup
										>)
									</th>
									<th class="text-center">Đơn giá (đ)</th>
									<th class="text-center">CLCL (%)</th>
									<th style="border-top-right-radius: 15px;" class="text-right">
										Thành tiền (đ)
									</th>
								</tr>
							</thead>
							<tbody class="vendorListHeadingResultBody">
								<tr
									v-for="(item, index) in data.step_3.tangible_assets"
									:key="'building' + index"
								>
									<td class="text-left">
										{{
											item.building_type
												? formatSentenceCase(item.building_type.description)
												: ""
										}}
									</td>
									<td class="text-center">
										{{ formatArea(item.total_construction_area) }}
										<!-- m<sup style="font-size: 11px"
											>2</sup
										> -->
									</td>
									<!-- <td class="text-center">{{ item.clcl + "  %" }}</td> -->
									<!-- <td class="text-right">{{ format(item.price) + " đ" }}</td> -->
									<td class="text-center">
										{{ formatNumber(item.unit_price) }}
									</td>
									<td class="text-center">{{ item.remaining_quality }}</td>
									<!-- <td class="text-right">{{ format(item.total) + " đ" }}</td> -->
									<td class="text-right">
										{{ formatNumber(item.total_price) }}
									</td>
								</tr>
								<tr class="summary">
									<td
										style="border-bottom-left-radius: 15px;"
										class="text-left font-weight-bold"
									>
										TỔNG CỘNG:
									</td>
									<!-- <td class="text-center">
										{{ formatArea(data.tangible_area) }}m<sup
											style="font-size: 11px"
											>2</sup
										>
									</td> -->
									<td class="text-center"></td>
									<td class="text-center"></td>
									<td class="text-center"></td>
									<!-- <td class="text-right">
										{{ format(data.tangible_total) + " đ" }}
									</td> -->
									<td
										style="border-bottom-right-radius: 15px;"
										class="text-right font-weight-bold"
									>
										{{
											formatNumber(
												Number(data.totalAllPrice) - Number(data.totalLandPrice)
											)
										}}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="card mb-4" v-if="!isApartment">
						<table cellspacing="0" cellpadding="0">
							<thead class="vendorListHeadingResult p-0">
								<tr>
									<th class="text-left totalResultLeft">TỔNG CỘNG:</th>
									<th class="text-right totalResultRight">
										{{ formatNumber(data.totalAllPrice) }}
									</th>
								</tr>
							</thead>
						</table>
					</div>

					<div
						class="card mb-4"
						v-if="
							isApartment &&
								data.step_3.apartment_finals &&
								data.step_3.apartment_finals.length > 0
						"
					>
						<table cellspacing="0" cellpadding="0">
							<thead class="vendorListHeadingResult p-0">
								<tr>
									<th style="border-top-left-radius: 15px;" class="text-left">
										Tên tài sản
									</th>
									<th class="text-center">
										Diện tích (m<sup style="font-size: 9px !important">2</sup>)
									</th>
									<th class="text-center">Đơn giá (đ)</th>
									<th style="border-top-right-radius: 15px;" class="text-right">
										Thành tiền (đ)
									</th>
								</tr>
							</thead>
							<tbody class="vendorListHeadingResultBody">
								<tr
									v-for="(item, index) in data.step_3.apartment_finals"
									:key="'detail' + index"
								>
									<td class="text-left">{{ item.name }}</td>

									<td class="text-center">
										{{ formatNumber(item.total_area) }}
										<!-- m<sup style="font-size: 11px"
											>2</sup
										> -->
									</td>
									<td class="text-center">
										{{ formatNumber(item.unit_price) }}
									</td>
									<td class="text-right">
										{{ formatNumber(item.total_price) }}
									</td>
									<!-- <td class="text-center">{{ format(item.price) + " đ" }}</td>
									<td class="text-right">{{ format(item.total) + " đ" }}</td> -->
								</tr>
							</tbody>
						</table>
					</div>

					<div
						v-if="
							data.step_3.image_planning_info &&
								data.step_3.image_planning_info.length > 0
						"
						style="margin-top: 300px;"
					>
						<div class="mt-5" style="color: #000; font-size: 12px !important">
							* Ảnh thông tin quy hoạch:
						</div>
						<div class="d-flex flex-column">
							<img
								v-for="image in data.step_3.image_planning_info"
								:src="image.link"
								height="300px"
								width="100%"
								style="margin:5px auto"
							/>
						</div>
						<div>
							<div class="d-flex">
								<div
									class="text-justify mb-2 mr-5 font-italic "
									style="color: #000; font-size: 12px !important; line-height: 1.8;"
									v-bind:class="['w-50']"
								>
									<!-- *Kết quả ước tính sơ bộ chỉ có giá trị tham khảo dựa trên các
							thông tin khách hàng cung cấp và chưa phải là kết quả thẩm định
							giá trên Chứng thư. -->

									* Biên độ chênh lệch: +/-
									{{ data.step_3.difference_amplitude }} %
									<br />
									* Ghi chú khác: <br />
									<div
										style="color: #000; font-size: 12px !important; line-height: 1.8;"
										v-html="formattedText"
									></div>
								</div>

								<div class="w-50 d-flex flex-column">
									<div class="row">
										<div class="text-left col-1"></div>
										<div class="text-left col-4 textPrint">Chữ ký</div>
										<div class="text-left col-1 textPrint">:</div>
										<div class="text-right col-6 textPrint">
											---------------------
										</div>
									</div>
									<div class="row">
										<div class="text-left col-1"></div>
										<div class="text-left col-4 textPrint">Người ước tính</div>
										<div class="text-left col-1 textPrint ">:</div>
										<div class="text-right col-6 textPrint">
											{{ data.created_by ? data.created_by.name : "" }}
										</div>
									</div>
									<div class="row">
										<div class="text-left col-1"></div>
										<div class="text-left col-4 textPrint">Thời điểm</div>
										<div class="text-left col-1 textPrint">:</div>
										<div class="text-right col-6 textPrint">
											{{ data.updated_at ? formatDate(data.updated_at) : "" }}
										</div>
									</div>
									<!-- <table>
								<tbody class="infoFooter">
									<tr>
										<td>
											Chữ ký
										</td>
										<td>
											:
										</td>
										<td>
											---------------------
										</td>
									</tr>
									<tr>
										<td>
											Người ước tính
										</td>
										<td>
											:
										</td>
										<td class="text-right">
											{{ data.created_by ? data.created_by.name : "" }}
										</td>
									</tr>
									<tr>
										<td>
											Thời điểm
										</td>
										<td>
											:
										</td>
										<td class="text-right">
											{{ data.updated_at ? formatDate(data.updated_at) : "" }}
										</td>
									</tr>
								</tbody>
							</table> -->

									<!-- <div class="report-info">
								<div class="report-label">Chữ ký:</div>

								<div class="report-value" style="text-align: right!important;">
							
								</div>
							</div> -->
									<!-- <div class="report-info">
								<div class="report-label">Người ước tính:</div>

								<div class="report-value" style="text-align: right!important;">
									{{ data.created_by ? data.created_by.name : "" }}
								</div>
							</div>
							<div class="report-info">
								<div class="report-label">Thời điểm:</div>

								<div class="report-value" style="text-align: right !important">
									{{ data.updated_at ? formatDate(data.updated_at) : "" }}
								</div>
							</div> -->
									<!-- <div>
								<div class="text-right result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
								<div
									class="text-right result-total-amount"
									style="text-align: right!important;"
								>
									{{ format(data.total) }} VND
								</div>
							</div> -->
								</div>
							</div>
							<div class="d-flex mt-n5 justify-content-end ">
								<table>
									<tbody class="infoSignature">
										<tr>
											<td class="text-center font-weight-bold">
												ĐẠI DIỆN PHÁP LUẬT
											</td>
										</tr>
										<tr class="mt-3">
											<td></td>
										</tr>
										<tr class="mt-3">
											<td></td>
										</tr>
										<tr class="mt-3">
											<td></td>
										</tr>
										<tr class="mt-3">
											<td></td>
										</tr>
										<tr>
											<td class="text-center font-weight-bold">
												Huỳnh Văn Ngoãn
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div v-else style="margin-top: 300px;">
						<div class="d-flex">
							<div
								class="text-justify mt-1 mb-2 mr-5 font-italic "
								style="color: #000; font-size: 12px !important; line-height: 1.8;"
								v-bind:class="['w-50']"
							>
								<!-- *Kết quả ước tính sơ bộ chỉ có giá trị tham khảo dựa trên các
							thông tin khách hàng cung cấp và chưa phải là kết quả thẩm định
							giá trên Chứng thư. -->

								* Biên độ chênh lệch: +/-
								{{ data.step_3.difference_amplitude }} %
								<br />
								* Ghi chú khác: <br />
								<div
									style="color: #000; font-size: 12px !important; line-height: 1.8;"
									v-html="formattedText"
								></div>
							</div>

							<div class="w-50 d-flex flex-column">
								<div class="row">
									<div class="text-left col-1"></div>
									<div class="text-left col-4 textPrint">Chữ ký</div>
									<div class="text-left col-1 textPrint">:</div>
									<div class="text-right col-6 textPrint">
										---------------------
									</div>
								</div>
								<div class="row">
									<div class="text-left col-1"></div>
									<div class="text-left col-4 textPrint">Người ước tính</div>
									<div class="text-left col-1 textPrint ">:</div>
									<div class="text-right col-6 textPrint">
										{{ data.created_by ? data.created_by.name : "" }}
									</div>
								</div>
								<div class="row">
									<div class="text-left col-1"></div>
									<div class="text-left col-4 textPrint">Thời điểm</div>
									<div class="text-left col-1 textPrint">:</div>
									<div class="text-right col-6 textPrint">
										{{ data.updated_at ? formatDate(data.updated_at) : "" }}
									</div>
								</div>
								<!-- <table>
								<tbody class="infoFooter">
									<tr>
										<td>
											Chữ ký
										</td>
										<td>
											:
										</td>
										<td>
											---------------------
										</td>
									</tr>
									<tr>
										<td>
											Người ước tính
										</td>
										<td>
											:
										</td>
										<td class="text-right">
											{{ data.created_by ? data.created_by.name : "" }}
										</td>
									</tr>
									<tr>
										<td>
											Thời điểm
										</td>
										<td>
											:
										</td>
										<td class="text-right">
											{{ data.updated_at ? formatDate(data.updated_at) : "" }}
										</td>
									</tr>
								</tbody>
							</table> -->

								<!-- <div class="report-info">
								<div class="report-label">Chữ ký:</div>

								<div class="report-value" style="text-align: right!important;">
							
								</div>
							</div> -->
								<!-- <div class="report-info">
								<div class="report-label">Người ước tính:</div>

								<div class="report-value" style="text-align: right!important;">
									{{ data.created_by ? data.created_by.name : "" }}
								</div>
							</div>
							<div class="report-info">
								<div class="report-label">Thời điểm:</div>

								<div class="report-value" style="text-align: right !important">
									{{ data.updated_at ? formatDate(data.updated_at) : "" }}
								</div>
							</div> -->
								<!-- <div>
								<div class="text-right result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
								<div
									class="text-right result-total-amount"
									style="text-align: right!important;"
								>
									{{ format(data.total) }} VND
								</div>
							</div> -->
							</div>
						</div>
						<div class="d-flex  justify-content-end ">
							<table>
								<tbody class="infoSignature">
									<tr>
										<td class="text-center font-weight-bold">
											ĐẠI DIỆN PHÁP LUẬT
										</td>
									</tr>
									<tr class="mt-3">
										<td></td>
									</tr>
									<tr class="mt-3">
										<td></td>
									</tr>
									<tr class="mt-3">
										<td></td>
									</tr>
									<tr class="mt-3">
										<td></td>
									</tr>
									<tr>
										<td class="text-center font-weight-bold">
											Huỳnh Văn Ngoãn
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer footer-print">
				<!-- v-print="'printBody'" -->
				<button v-print="printObj" @click="statusPrint" class="btn btn-orange">
					In
				</button>
			</div>
		</div>
	</div>
	<!-- <div
		v-else
		class="modal-delete"
		style="padding: 0;"
		@click.self="handleCancel"
	>
		<div class="card">
			<div class="card-header d-flex justify-content-end align-items-center">
				<img
					@click="handleCancel"
					src="../../assets/icons/ic_cancel-1.svg"
					alt="icon"
				/>
			</div>
			<div class="card-body" id="printBody" style="padding: 10px;">
				<img src="@/assets/images/header-kqsb.png" />
				<div class="text-right" style="color: #000">
					Mã sơ bộ: {{ data.id ? "TSSB_" + data.id : "" }}
				</div>
				<div class="title__property text-center">KẾT QUẢ ƯỚC TÍNH SƠ BỘ</div>
				<div class="w-100">
					<div class="card mb-3">
						<div class="card-header vendorListHeading">Mô tả tài sản</div>
						<div class="card-body">
							<div class="data-row">
								<div class="data-label">Loại tài sản:</div>
								<div class="data-value">
									{{ data.asset_type ? data.asset_type.description : "" }}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Vị trí:</div>
								<div class="data-value">
									{{ data.properties ? data.properties[0].description : "" }}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Tọa độ:</div>
								<div class="data-value">
									{{ data.coordinates ? data.coordinates : "" }}
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Số tờ/số thửa:</div>
								<div class="data-value">
									<InputText v-model="data.doc_num" vid="doc_num" class="" />
								</div>
							</div>
							<div class="data-row">
								<div class="data-label">Người yêu cầu:</div>
								<div class="data-value">
									<InputText v-model="person" vid="person" class="" />
								</div>
							</div>
						</div>
					</div>
					<div class="main-map">
						<div class="layer-map">
							<l-map
								ref="lmap"
								:zoom="zoom"
								:center="[
									data.coordinates.split(',')[0],
									data.coordinates.split(',')[1]
								]"
								:options="{ zoomControl: false }"
								:maxZoom="20"
							>
								<l-tile-layer
									:url="url"
									:options="{ maxNativeZoom: 19, maxZoom: 20 }"
								></l-tile-layer>
								<l-control-zoom position="bottomright"></l-control-zoom>
								<l-marker
									:lat-lng="[
										data.coordinates.split(',')[0],
										data.coordinates.split(',')[1]
									]"
								>
									<l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
										<img
											style="width: 60px !important"
											class="icon_marker"
											src="@/assets/images/svg_home.svg"
											alt=""
										/>
									</l-icon>
								</l-marker>
							</l-map>
						</div>
					</div>
					<div class="card mb-3" v-if="data.assets && data.assets.length > 0">
						<table>
							<thead class="vendorListHeading p-0">
								<tr>
									<th class="text-center">Quyền sử dụng đất</th>
									<th class="text-center">Loại đất</th>
									<th class="text-center">Diện tích</th>
									<th class="text-center">Đơn giá</th>
									<th class="text-center">Thành tiền</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in data.assets"
									:key="'detail' + index"
								>
									<td class="text-center">{{ item.description }}</td>
									<td class="text-center">
										{{ formatSentenceCase(item.land_type_description) }}
									</td>
									<td class="text-right">
										{{ formatArea(item.area) }}m<sup style="font-size: 11px"
											>2</sup
										>
									</td>
									<td class="text-right">{{ format(item.price) + " đ" }}</td>
									<td class="text-right">{{ format(item.total) + " đ" }}</td>
								</tr>
								<tr class="summary">
									<td class="text-center">Tổng cộng:</td>
									<td class="text-right"></td>
									<td class="text-right">
										{{ formatArea(data.land_area) }}m<sup
											style="font-size: 11px"
											>2</sup
										>
									</td>
									<td class="text-right"></td>
									<td class="text-right">
										{{ format(data.land_total) + " đ" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div
						class="card mb-3"
						v-if="data.step_3.tangible_assets && data.step_3.tangible_assets.length > 0"
					>
						<table>
							<thead class="vendorListHeading p-0">
								<tr>
									<th class="text-center">Loại công trình</th>
									<th class="text-center">Diện tích sàn xây dựng</th>
									<th class="text-center">% Chất lượng còn lại</th>
									<th class="text-center">Đơn giá</th>
									<th class="text-center">Thành tiền</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in data.step_3.tangible_assets"
									:key="'building' + index"
								>
									<td class="text-center">
										<div class="text-center">
											{{ item.name ? item.name : "" }}
										</div>
									</td>
									<td class="text-center">
										{{ formatArea(item.area) }}m<sup style="font-size: 11px"
											>2</sup
										>
									</td>
									<td class="text-center">{{ item.clcl + "  %" }}</td>
									<td class="text-right">{{ format(item.price) + " đ" }}</td>
									<td class="text-right">{{ format(item.total) + " đ" }}</td>
								</tr>
								<tr class="summary">
									<td class="text-center">Tổng cộng:</td>
									<td class="text-center">
										{{ formatArea(data.tangible_area) }}m<sup
											style="font-size: 11px"
											>2</sup
										>
									</td>
									<td class="text-center"></td>
									<td class="text-center"></td>
									<td class="text-right">
										{{ format(data.tangible_total) + " đ" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="text-danger mb-2">
						*Kết quả ước tính sơ bộ chỉ có giá trị tham khảo và có thể thay đổi
						sau khi kiểm tra hiện trạng tài sản thực tế
					</div>
					<div style="break-inside: avoid">
						<div class="report-info">
							<div class="report-label">Người ước tính:</div>
							<div class="report-value">
								{{ data.created_by ? data.created_by.name : "" }}
							</div>
						</div>
						<div class="report-info">
							<div class="report-label">Thời điểm:</div>
							<div class="report-value">
								{{ data.updated_at ? formatDate(data.updated_at) : "" }}
							</div>
						</div>
						<div>
							<div class="text-right result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
							<div class="text-right result-total-amount">
								{{ format(data.total) }} VND
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer footer-print" style="margin-top: 30px;">
				<button
					v-print="'printBody'"
					@click="statusPrint"
					class="btn btn-orange"
				>
					In
				</button>
			</div>
		</div>
	</div> -->
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import {
	LMap,
	LTileLayer,
	LMarker,
	LIcon,
	LControlZoom,
	LPopup,
	LCircle,
	LTooltip,
	LLayerGroup
} from "vue2-leaflet";
import print from "vue-print-nb";
import InputText from "../Form/InputText.vue";
import moment from "moment";
export default {
	name: "ModalPrintEstimateAsset",
	props: ["data", "isApartment"],
	directives: {
		print
	},
	components: {
		LMap,
		LTileLayer,
		LMarker,
		LIcon,
		LPopup,
		LCircle,
		LControlZoom,
		LTooltip,
		LLayerGroup,
		InputText
	},

	data() {
		return {
			printObj: {
				id: "printBody",
				popTitle:
					"TSSB_" +
					this.data.id +
					(this.data.step_3.petitioner_name
						? "_" + this.data.step_3.petitioner_name.replaceAll(" ", "_")
						: "") +
					(this.data.createdAtString ? "_" + this.data.createdAtString : "")
			},
			apartment_name: "",
			land_types: [],
			asset_details: [],
			createdAt: "",
			zoom: 15,
			caller: null,
			url: "https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}",
			bounds: null,
			person: "",
			doc_num: ""
		};
	},
	mounted() {
		this.zoom = 16;
		this.getCreatedAt();
		this.printObj = {
			id: "printBody",
			popTitle:
				"TSSB_" +
				this.data.id +
				(this.data.step_3.petitioner_name
					? "_" + this.data.step_3.petitioner_name.replaceAll(" ", "_")
					: "") +
				(this.data.createdAtString ? "_" + this.data.createdAtString : "")
		};
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
			if (!phrase) return "";
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		getCreatedAt() {
			const today = new Date();
			this.createdAt =
				`${today.getHours() < 10 ? "0" + today.getHours() : today.getHours()}` +
				":" +
				`${
					today.getMinutes() < 10
						? "0" + today.getMinutes()
						: today.getMinutes()
				}` +
				" " +
				`${today.getDate() < 10 ? "0" + today.getDate() : today.getDate()}` +
				"/" +
				`${
					today.getMonth() + 1 < 10
						? "0" + (today.getMonth() + 1)
						: today.getMonth() + 1
				}` +
				"/" +
				today.getFullYear();
		},
		format(value) {
			let num = (value / 1).toFixed(0).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		formatArea(value) {
			let num = (value / 1).toFixed(2).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		formatDate(value) {
			return moment(String(value)).format("HH:mm [Ngày] DD/MM/YYYY");
		},
		formatNumber(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},

		statusPrint(event) {
			this.printObj = {
				id: "printBody",
				popTitle:
					"TSSB_" +
					this.data.id +
					(this.data.step_3.petitioner_name
						? "_" + this.data.step_3.petitioner_name.replaceAll(" ", "_")
						: "") +
					(this.data.createdAtString ? "_" + this.data.createdAtString : "")
			};

			const originalTitle = document.title;

			document.title = this.printObj.popTitle;

			setTimeout(() => {
				document.title = originalTitle;
			}, 3000);
		},
		handleAction(event) {
			this.$emit("action", event);
			this.$emit("cancel", event);
		}
	},
	computed: {
		formattedText() {
			return this.data.step_3.note
				? this.data.step_3.note.replaceAll(/\n/g, "<br>")
				: "";
		}
	}
};
</script>

<style lang="scss" scoped>
#printBody {
	padding: 30px 50px;

	* {
		text-align: left;
		@media print {
			-webkit-print-color-adjust: exact;
			font-size: 1rem !important;
		}
	}

	.card {
		border: none;
		// border-radius: 10px;
		// border-radius: 5px;
		// border: 1px solid rgba(110, 117, 130, 0.2);
		box-shadow: none;
		overflow: hidden;

		* {
			font-size: 14px;
			color: #000000;
			font-weight: 500;
		}
	}

	.vendorListHeading {
		// background: #f28c1c;
		// border-top-left-radius: 15px;
		// border-top-right-radius: 15px;
		// border-style: solid;
		font-size: 18px !important;
		font-weight: bold;
		color: #0685b2;
		tr {
			th {
				font-weight: bold;
				color: #0685b2;
			}
		}
	}
	.vendorListHeadingResult {
		background-color: #f7f7f7;
		// border: 1px;
		border-bottom-left-radius: 15px;
		border-bottom-right-radius: 15px;
		// border-style: solid;
		tr {
			th {
				border-top: 0;
				border-left: 0;
				border-right: 0;
				border-bottom: 2px white;
				border-style: solid;
				font-weight: bold;
				font-size: 12px !important;
			}
		}
	}
	.vendorListHeadingResultBody {
		background-color: #f7f7f7;
		tr {
			border: 0;

			td {
				border: 0;
				font-size: 12px !important;
			}
		}
	}
	.infoSignature {
		tr {
			border: 0;
			td {
				padding: 5px;
				border: 0;
				font-size: 12px !important;

				color: #000000;
			}
		}
	}
	.textPrint {
		border: 0;
		line-height: 1.8;
		font-size: 12px !important;
		font-style: italic;
		color: #000000;
	}
	.infoFooter {
		tr {
			border: 0;
			padding: 0 !important;
			line-height: 0 !important;
			td {
				line-height: 0 !important;
				// padding: 5px;
				border: 0;
				font-size: 12px !important;
				font-style: italic;
				color: #000000;
			}
		}
	}
	.totalResultLeft {
		border-top-left-radius: 15px;
		border-bottom-left-radius: 15px;
		font-weight: bold;
		color: #128bcb;
	}
	.totalResultRight {
		border-top-right-radius: 15px;
		border-bottom-right-radius: 15px;
		font-weight: bold;
		color: #128bcb;
	}
	.card-header {
		padding-bottom: 5px;
		border-bottom: 3px solid #0685b2;
		padding-left: 0px;
	}

	.card-body {
		padding: 0;
	}

	.data-row {
		// border-bottom: 1px solid rgba(110, 117, 130, 0.2);
		display: inline-block;
		width: 100%;
		// box-sizing: border-box;

		&:nth-last-child(1) {
			border-bottom: none;
		}
		&--apartment {
			width: 50%;
			float: left;
			border-bottom: none;
			.title,
			.content {
				float: left;
				display: inline-block;
				width: 50%;
				box-sizing: border-box;
			}
		}
		&--address {
			width: 100%;
			.title {
				width: 25%;
			}
			.content {
				width: 75%;
			}
		}
	}

	.data-label {
		display: inline-block;
		// font-weight: bold;
		// padding: 5px 10px;
		padding-left: 0px;
		width: 20%;
		box-sizing: border-box;
		font-size: 12px !important;
		float: left;
		&--margin {
			margin-top: 8px;
		}
	}

	.data-value {
		display: inline-block;
		font-weight: bold;
		font-size: 12px !important;
		// padding-left: ;
		// padding: 5px 10px;
		width: 80%;
		box-sizing: border-box;
		float: right;

		input {
			padding: 3px 5px;
		}
	}

	td,
	th {
		padding: 10px;
		border-right: 1px solid rgba(110, 117, 130, 0.2);
		&:last-child {
			border-right: none;
		}
	}

	tr {
		border-bottom: 1px solid rgba(110, 117, 130, 0.2);

		&:nth-last-child(1) {
			border-bottom: none;
		}
	}

	.summary {
		background: #f7f7f7;

		// td {
		// 	color: #000000;
		// 	font-weight: bold;
		// }
	}

	.report-info {
		display: inline-block;
		width: 100%;
		color: #000000;
		.report-label {
			white-space: nowrap;
			font-size: 12px !important;
			width: 20%;
			display: inline-block;
			box-sizing: border-box;
			// font-weight: bold;
			font-style: italic;
			float: left;
		}

		.report-value {
			// width: 80%;
			font-size: 12px !important;
			display: inline-block;
			box-sizing: border-box;
			font-style: italic;
			// font-weight: bold;
			float: right;
		}
	}

	.result-total {
		color: #f28c1c;
		font-weight: bold;
		font-size: 15px;
		text-align: right;
	}

	.result-total-amount {
		font-weight: bold;
		font-size: 30px;
		color: #f28c1c;
		text-align: right;
		&--error {
			margin-top: 10px;
			color: #ef3039;
			font-weight: 500;
			font-size: 23px;
		}
	}
	.result-apartment {
		width: 100%;
		display: inline-block;
		border-bottom: 1px solid rgba(110, 117, 130, 0.2);
		.result-total-amount,
		.result-total {
			display: inline-block;
			float: left;
			box-sizing: border-box;
		}
		.result-total {
			vertical-align: bottom;
			text-align: left !important;
			width: 30%;
		}
		.result-total-amount {
			text-align: right;
			width: 70%;
		}
	}
	.container {
		&__description {
			padding: 8px 20px;
			border-bottom: 1px solid #d0d0d0;
			&:last-child {
				border-bottom: none;
			}
			.title,
			.content {
				padding: 0;
				color: #000000;
				margin-bottom: 0;
			}
			.title {
				font-weight: 600;
				font-size: 14px;
			}
			.content {
				white-space: nowrap;
				@media (max-width: 767px) {
					white-space: normal;
				}
				&--full-address {
					white-space: normal;
				}
			}
			&--apartment {
				display: inline-block;
				float: left;
				width: 100%;
			}
		}
	}
}
.footer-print {
	display: flex;
	justify-content: flex-end;
	padding: 0.75rem 50px;
}
.modal-delete {
	position: fixed;
	z-index: 10002;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}

	.card {
		max-width: 1062px;
		width: 100%;
		margin-bottom: 0;

		&-header {
			border-bottom: none;

			h3 {
				color: #333333;
			}

			img {
				cursor: pointer;
			}
		}

		&-body {
			text-align: center;
			padding: 8px 20px 20px;
			max-height: 80vh;
			overflow-y: auto;

			p {
				color: #333333;
				margin-bottom: 40px;
			}
		}
	}
}

.title__property {
	text-align: center;
	font-size: 25px;
	font-weight: 600;
	color: #000000;
	margin-bottom: 30px;
}

.container {
	&__property {
		height: 100%;
		border: 1px solid #d0d0d0;
		padding: 15px;
		border-radius: 5px;
		@media (max-width: 1023px) {
			margin-bottom: 20px;
		}

		.property {
			&__detail {
				font-size: 14px;
				color: #000000;
				margin-bottom: 5px;

				.name,
				.content {
					margin-bottom: 0;
					padding: 0 !important;
				}

				.name {
					text-align: left;
					width: 50%;
					white-space: nowrap;
					text-overflow: ellipsis;
					overflow: hidden;
				}

				.content {
					color: #333333;
					display: block;
					text-align: end;

					&__id {
						color: #faa831;
					}
				}
			}
		}
	}
}

.property {
	&__title {
		color: #333333;
		font-size: 14px;
		text-decoration: underline;
		margin-bottom: 10px !important;
		text-align: left;
	}
}
.main-map {
	position: relative;
	height: 300px;
	width: 100%;
	transition-timing-function: ease;
	transition-duration: 0.25s;
	overflow-x: hidden;
	margin-bottom: 15px;
	border-radius: 5px;
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
</style>
