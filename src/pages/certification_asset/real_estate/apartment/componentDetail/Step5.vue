<template>
	<div>
		<div class="card mb-0">
			<div class="card-body card-info" v-show="showDetailContruction">
				<div>
					<Tabs class="tab_step_7" :theme="theme" noTouch :navSlider="false" :navAuto="true">
						<TabItem name="Bảng tổng hợp thông tin">
							<div class="content_detail_asset">
								<ValidationObserver tag="form" ref="step_7_tab1">
									<div class="ant-table ant-table-scroll-position-left ant-table-default">
										<div class="ant-table-content">
											<div class="ant-table-body">
												<table class="table_detail_property color_content" v-if="apartment.assets_general && apartment.assets_general.length > 0">
														<thead class="ant-table-thead">
															<tr>
																<th>TT</th>
																<th>Chỉ tiêu</th>
																<th>TSTĐ</th>
																<th v-for="(asset, index) in apartment.assets_general" :key="'header' + index" >{{ 'TSSS ' + asset.id }}</th>
															</tr>
														</thead>
														<tbody class="ant-table-tbody">
															<tr>
																<td>1</td>
																<td>Loại tài sản</td>
																<td>{{data.asset_type ? formatSentenceCase(data.asset_type.description) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.asset_type ? formatSentenceCase(asset.asset_type.description) : '' }}</td>
															</tr>
															<tr>
																<td>2</td>
																<td>Tên chung cư</td>
																<td>{{data.project ? data.project.name : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.project ? asset.project.name : "-" }}</td>
															</tr>
															<tr>
																<td>3</td>
																<td>Địa chỉ</td>
																<td>{{data.full_address ? data.full_address : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.full_address ? asset.full_address : "-" }}</td>
															</tr>
															<tr>
																<td>4</td>
																<td>Thời điểm giao dịch</td>
																<td>-</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{ formatSentenceCase(asset.transaction_type.description) }}</td>
															</tr>
															<tr>
																<td>4</td>
																<td>Tọa độ</td>
																<td>
																		<div>
																				<div>{{data.coordinates ? `${data.coordinates.split(',')[0]},` : "-"}}</div>
																				<div>{{data.coordinates  ? `${data.coordinates.split(',')[1]}` : "-"}}</div>
																		</div>
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">
																		<div>
																				<div>{{ asset.coordinates ? `${asset.coordinates.split(',')[0]},` : "-"}}</div>
																				<div>{{ asset.coordinates ? `${asset.coordinates.split(',')[1]}` : "-"}}</div>
																		</div>
																</td>
															</tr>
															<tr>
																<td>6</td>
																<td>Pháp lý</td>
																<td>{{data.apartment_asset_properties  ? formatSentenceCase(data.apartment_asset_properties.legal.description) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.room_details && asset.room_details.length > 0 && asset.room_details[0].legal ? formatSentenceCase(asset.room_details[0].legal.description) : "-" }}</td>
															</tr>
															<tr>
																<td>7</td>
																<td>Loại căn hộ</td>
																<td>{{data.apartment_asset_properties && data.apartment_asset_properties.rank  ? formatSentenceCase(data.apartment_asset_properties.rank.description) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.apartment_specification && asset.apartment_specification.rank ? formatSentenceCase(asset.apartment_specification.rank.description) : "-" }}</td>
															</tr>
															<tr>
																<td>8</td>
																<td>Block</td>
																<td>{{data.apartment_asset_properties  ? formatSentenceCase(data.apartment_asset_properties.block.name) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.block ? formatSentenceCase(asset.block.name) : "-" }}</td>
															</tr>
															<tr>
																<td>9</td>
																<td>Tầng</td>
																<td>{{data.apartment_asset_properties  ? formatSentenceCase(data.apartment_asset_properties.floor.name) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.floor ? formatSentenceCase(asset.floor.name) : "-" }}</td>
															</tr>
															<tr>
																<td>10</td>
																<td>Mã căn hộ</td>
																<td>{{data.apartment_asset_properties  ? formatSentenceCase(data.apartment_asset_properties.apartment_name) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.apartment_specification ? formatSentenceCase(asset.apartment_specification.apartment_name) : "-" }}</td>
															</tr>
															<tr>
																<td>11</td>
																<td>Số phòng ngủ</td>
																<td>{{data.apartment_asset_properties  ? data.apartment_asset_properties.bedroom_num : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.room_details && asset.room_details.length > 0 && asset.room_details[0].bedroom_num ? asset.room_details[0].bedroom_num : "-" }}</td>
															</tr>
															<tr>
																<td>12</td>
																<td>Số phòng WC</td>
																<td>{{data.apartment_asset_properties  ? data.apartment_asset_properties.wc_num : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.room_details && asset.room_details.length > 0 && asset.room_details[0].wc_num ? asset.room_details[0].wc_num : "-" }}</td>
															</tr>
															<tr>
																<td>13</td>
																<td>Tình trạng nội thất</td>
																<td>{{data.apartment_asset_properties  ? formatSentenceCase(data.apartment_asset_properties.furniture_quality.description) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.room_details && asset.room_details.length > 0 && asset.room_details[0].furniture_quality ? formatSentenceCase(asset.room_details[0].furniture_quality.description) : "-" }}</td>
															</tr>
															<tr>
																<td>14</td>
																<td>Tiện ích</td>
																<td>
																	<div v-if="data.apartment_asset_properties && data.apartment_asset_properties.utility_description && data.apartment_asset_properties.utility_description.length > 0">
																		<div v-for="(utility, indexData) in data.apartment_asset_properties.utility_description" :key="indexData">{{utility}}</div>
																	</div>
																	<div v-else>-</div>
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">
																	<div v-if="asset.apartment_specification && asset.apartment_specification.utility_description && asset.apartment_specification.utility_description.length > 0">
																		<div v-for="(utility, indexData) in asset.apartment_specification.utility_description" :key="indexData">{{utility}}</div>
																	</div>
																	<div v-else>-</div>
																</td>
															</tr>
															<tr>
																<td>15</td>
																<td>Hướng chính</td>
																<td>{{data.apartment_asset_properties  ? formatSentenceCase(data.apartment_asset_properties.direction.description) : "-"}}</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetType' + index">{{asset.room_details && asset.room_details.length > 0 && asset.room_details[0].direction ? formatSentenceCase(asset.room_details[0].direction.description) : "-" }}</td>
															</tr>
															<tr>
																<td>16</td>
																<td>Giá rao bán</td>
																<td>-</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'totalAmount' + index">{{ formatNumber(asset.total_amount) }} đ</td>
															</tr>
															<tr>
																<td>17</td>
																<td>Tỷ lệ giá rao bán</td>
																<td></td>
																<td v-for="(asset, index) in form.apartment_adapter" :key="'adjustPercent' + index">
																		<InputPercent
																			:disabled="!isEditStatus"
																			class="label-none input_center"
																			v-model="asset.percent"
																			vid="number_legal"
																			label="Tỷ lệ"
																			:max="999999999"
																			:min="-999999999"
																			:text_center="true"
																			@change="changePercentSaleRating($event, index)"
																		/>
																</td>
															</tr>
															<tr>
																<td>18</td>
																<td>Tổng giá trị tài sản ước tính</td>
																<td></td>
																<td>{{formatNumber(totalPriceEstimate1)}}đ</td>
																<td>{{formatNumber(totalPriceEstimate2)}}đ</td>
																<td>{{formatNumber(totalPriceEstimate3)}}đ</td>
															</tr>
															<tr>
																<td>19</td>
																<td>Đ/giá B.Quân.</td>
																<td></td>
																<td>{{formatNumber(parseFloat(dgcc1).toFixed(0))}}đ</td>
																<td>{{formatNumber(parseFloat(dgcc2).toFixed(0))}}đ</td>
																<td>{{formatNumber(parseFloat(dgcc3).toFixed(0))}}đ</td>
															</tr>
														</tbody>
												</table>
											</div>
										</div>

									</div>
									<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
										<div class="d-md-flex d-block">
											<button  @click="onCancel" class="btn btn-white text-nowrap" >
												<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Thoát
											</button>
											<button v-if="isEditStatus" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="handleSaveTab1">
												<img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
											</button>
										</div>
									</div>
								</ValidationObserver>
							</div>
						</TabItem>
						<TabItem class="item_2" name="Bảng điều chỉnh QSDĐ">
							<div class="content_detail_asset">
								<div class="ant-table ant-table-scroll-position-left ant-table-default">
										<div class="ant-table-content">
											<div class="ant-table-body">
												<table class="table_comparision color_content" v-if="apartment.comparison_factor && apartment.comparison_factor.length > 0">
													<thead class="ant-table-thead">
															<tr>
																<th>TT</th>
																<th>Yếu tố so sánh</th>
																<th>TSTĐ</th>
																<th v-for="(asset, index) in apartment.comparison_factor" :key="'headerElement' + index" >{{ 'TSSS ' + asset.id }}</th>
															</tr>
													</thead>
													<tbody class="ant-table-tbody">
															<tr>
																<td>1</td>
																<td>Đơn giá quyền sử dụng đất (đồng/m<sup>2</sup>)</td>
																<td>Chưa biết</td>
																<td>{{formatNumber(parseFloat(dgcc1).toFixed(0))}}đ</td>
																<td>{{formatNumber(parseFloat(dgcc2).toFixed(0))}}đ</td>
																<td>{{formatNumber(parseFloat(dgcc3).toFixed(0))}}đ</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'appraisalLegalRow' + index">
																<td rowspan="4">A</td>
																<td><strong>Pháp lý</strong></td>
																<td v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'appraisalLegal' + index">
																		{{formatSentenceCase(comparison_factor_appraise.apartment_title)}}
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetInfo' + index">
																	<span  v-for="(comparison_factor, indexItem) in apartment.comparison_factor[index].comparison_factor"  :key="'phap_ly' + indexItem">
																		<span v-if="comparison_factor.type === 'phap_ly'">
																			{{formatSentenceCase(comparison_factor.asset_title)}}
																		</span>
																	</span>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'changeLegalRow' + index">
																<td>Tỷ lệ điều chỉnh</td>
																<td></td>
																<td v-for="(asset, indexAsset) in apartment.assets_general" :key="'inputLegal' + indexAsset">
																	<div v-for="(comparison_factor, index) in apartment.comparison_factor[indexAsset].comparison_factor"  :key="'phap_ly' +index">
																		<div v-if="comparison_factor.type === 'phap_ly'">
																			<InputPercentNegative
																				:disabled="!isEditStatus"
																				class="label-none input_center"
																				v-model="comparison_factor.adjust_percent"
																				vid="number_legal"
																				:text_center="true"
																				@change="changeLegalRate($event, indexAsset, comparison_factor.type)"
																			/>
																		</div>
																	</div>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'LegalRow1' + index">
																<td>Mức điều chỉnh</td>
																<td></td>
																<td>{{formatNumber(parseFloat(pricePl1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(pricePl2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(pricePl3).toFixed(0))}}</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'LegalRow2' + index">
																<td><strong>Giá sau điều chỉnh</strong></td>
																<td></td>
																<td><strong>{{formatNumber(parseFloat(totalPricePL1).toFixed(0))}}</strong></td>
																<td><strong>{{formatNumber(parseFloat(totalPricePL2).toFixed(0))}}</strong></td>
																<td><strong>{{formatNumber(parseFloat(totalPricePL3).toFixed(0))}}</strong></td>
															</tr>

															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'loai_can' && comparison_factor_appraise.status === 1)" :key="'loaicanho' + index">
																<td rowspan="4">
																	<div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('loai_can')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
																</td>
																<td><strong>Loại căn hộ</strong></td>
																<td v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'loai_can' && comparison_factor_appraise.status === 1)" :key="'loaicanho1' + index">
																	{{formatSentenceCase(comparison_factor_appraise.apartment_title)}}
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetLoaiCan' + index">
																	<span v-for="(comparison_factor, indexItem) in apartment.comparison_factor[index].comparison_factor"  :key="'loaicanho2' +indexItem">
																		<span v-if="comparison_factor.type === 'loai_can'">
																			{{formatSentenceCase(comparison_factor.asset_title)}}
																		</span>
																	</span>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'loai_can' && comparison_factor_appraise.status === 1)" :key="'loaicanho3' + index">
																<td>Tỷ lệ điều chỉnh</td>
																<td></td>
																<td v-for="(asset, indexAsset) in apartment.assets_general" :key="'inputLoaiCan' + indexAsset">
																	<div v-for="(comparison_factor, index) in apartment.comparison_factor[indexAsset].comparison_factor"  :key="'loaicanho4' +index">
																		<div v-if="comparison_factor.type === 'loai_can'">
																			<InputPercentNegative
																				:disabled="!isEditStatus"
																				class="label-none input_center"
																				v-model="comparison_factor.adjust_percent"
																				vid="number_legal"
																				:text_center="true"
																				@change="changeLegalRate($event, indexAsset, comparison_factor.type)"
																			/>
																		</div>
																	</div>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'loai_can' && comparison_factor_appraise.status === 1)" :key="'loaicanho5' + index">
																<td>Mức điều chỉnh</td>
																<td></td>
																<td>{{formatNumber(parseFloat(priceLch1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(priceLch2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(priceLch3).toFixed(0))}}</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'loai_can' && comparison_factor_appraise.status === 1)" :key="'loaicanho6' + index">
																<td><strong>Giá sau điều chỉnh</strong></td>
																<td></td>
																<td><strong>{{formatNumber(adjustPriceData['loai_can'][0])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['loai_can'][1])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['loai_can'][2])}}</strong></td>
															</tr>

															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dien_tich' && comparison_factor_appraise.status === 1)" :key="'dientich' + index">
																<td rowspan="4">
																	<div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('dien_tich')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
																</td>
																<td><strong>Diện tích</strong></td>
																<td v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dien_tich' && comparison_factor_appraise.status === 1)" :key="'dientich1' + index">
																	{{parseFloat(comparison_factor_appraise.apartment_title) ? formatNumber(parseFloat(comparison_factor_appraise.apartment_title)) : comparison_factor_appraise.apartment_title}}m<sup>2</sup>
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetDienTich' + index">
																	<span v-for="(comparison_factor, indexItem) in apartment.comparison_factor[index].comparison_factor"  :key="'dientich2' +indexItem">
																		<span v-if="comparison_factor.type === 'dien_tich'">
																			{{parseFloat(comparison_factor.asset_title) ? formatNumber(parseFloat(comparison_factor.asset_title)) : comparison_factor.asset_title}}m<sup>2</sup>
																		</span>
																	</span>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dien_tich' && comparison_factor_appraise.status === 1)" :key="'dientich3' + index">
																<td>Tỷ lệ điều chỉnh</td>
																<td></td>
																<td v-for="(asset, indexAsset) in apartment.assets_general" :key="'inputDienTich' + indexAsset">
																	<div v-for="(comparison_factor, index) in apartment.comparison_factor[indexAsset].comparison_factor"  :key="'dientich4' +index">
																		<div v-if="comparison_factor.type === 'dien_tich'">
																			<InputPercentNegative
																				:disabled="!isEditStatus"
																				class="label-none input_center"
																				v-model="comparison_factor.adjust_percent"
																				vid="number_legal"
																				:text_center="true"
																				@change="changeLegalRate($event, indexAsset, comparison_factor.type)"
																			/>
																		</div>
																	</div>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dien_tich' && comparison_factor_appraise.status === 1)" :key="'dientich5' + index">
																<td>Mức điều chỉnh</td>
																<td></td>
																<td>{{formatNumber(parseFloat(priceDt1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(priceDt2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(priceDt3).toFixed(0))}}</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dien_tich' && comparison_factor_appraise.status === 1)" :key="'dientich6' + index">
																<td><strong>Giá sau điều chỉnh</strong></td>
																<td></td>
																<td><strong>{{formatNumber(adjustPriceData['dien_tich'][0])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['dien_tich'][1])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['dien_tich'][2])}}</strong></td>
															</tr>

															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'tang' && comparison_factor_appraise.status === 1)" :key="'tang' + index">
																<td rowspan="4">
																	<div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('tang')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
																</td>
																<td><strong>Số tầng</strong></td>
																<td v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'tang' && comparison_factor_appraise.status === 1)" :key="'tang1' + index">
																	{{formatSentenceCase(comparison_factor_appraise.apartment_title)}}
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetSoTang' + index">
																	<span v-for="(comparison_factor, indexItem) in apartment.comparison_factor[index].comparison_factor"  :key="'tang2' +indexItem">
																		<span v-if="comparison_factor.type === 'tang'">
																			{{formatSentenceCase(comparison_factor.asset_title)}}
																		</span>
																	</span>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'tang' && comparison_factor_appraise.status === 1)" :key="'tang3' + index">
																<td>Tỷ lệ điều chỉnh</td>
																<td></td>
																<td v-for="(asset, indexAsset) in apartment.assets_general" :key="'inputSoTang' + indexAsset">
																	<div v-for="(comparison_factor, index) in apartment.comparison_factor[indexAsset].comparison_factor"  :key="'tang4' +index">
																		<div v-if="comparison_factor.type === 'tang'">
																			<InputPercentNegative
																				:disabled="!isEditStatus"
																				class="label-none input_center"
																				v-model="comparison_factor.adjust_percent"
																				vid="number_legal"
																				:text_center="true"
																				@change="changeLegalRate($event, indexAsset, comparison_factor.type)"
																			/>
																		</div>
																	</div>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'tang' && comparison_factor_appraise.status === 1)" :key="'tang5' + index">
																<td>Mức điều chỉnh</td>
																<td></td>
																<td>{{formatNumber(parseFloat(priceSt1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(priceSt2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(priceSt3).toFixed(0))}}</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'tang' && comparison_factor_appraise.status === 1)" :key="'tang6' + index">
																<td><strong>Giá sau điều chỉnh</strong></td>
																<td></td>
																<td><strong>{{formatNumber(adjustPriceData['tang'][0])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['tang'][1])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['tang'][2])}}</strong></td>
															</tr>

															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_wc' && comparison_factor_appraise.status === 1)" :key="'sophongwc' + index">
																<td rowspan="4">
																	<div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('so_phong_wc')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
																</td>
																<td><strong>Số phòng WC</strong></td>
																<td v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_wc' && comparison_factor_appraise.status === 1)" :key="'sophongwc1' + index">
																	{{formatSentenceCase(comparison_factor_appraise.apartment_title)}}
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetSoPhongWC' + index">
																	<span v-for="(comparison_factor, indexItem) in apartment.comparison_factor[index].comparison_factor"  :key="'sophongwc2' +indexItem">
																		<span v-if="comparison_factor.type === 'so_phong_wc'">
																			{{formatSentenceCase(comparison_factor.asset_title)}}
																		</span>
																	</span>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_wc' && comparison_factor_appraise.status === 1)" :key="'sophongwc3' + index">
																<td>Tỷ lệ điều chỉnh</td>
																<td></td>
																<td v-for="(asset, indexAsset) in apartment.assets_general" :key="'inputSoPhongWC' + indexAsset">
																	<div v-for="(comparison_factor, index) in apartment.comparison_factor[indexAsset].comparison_factor"  :key="'sophongwc4' +index">
																		<div v-if="comparison_factor.type === 'so_phong_wc'">
																			<InputPercentNegative
																				:disabled="!isEditStatus"
																				class="label-none input_center"
																				v-model="comparison_factor.adjust_percent"
																				vid="number_legal"
																				:text_center="true"
																				@change="changeLegalRate($event, indexAsset, comparison_factor.type)"
																			/>
																		</div>
																	</div>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_wc' && comparison_factor_appraise.status === 1)" :key="'sophongwc5' + index">
																<td>Mức điều chỉnh</td>
																<td></td>
																<td>{{formatNumber(parseFloat(pricePWC1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(pricePWC2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(pricePWC3).toFixed(0))}}</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_wc' && comparison_factor_appraise.status === 1)" :key="'sophongwc6' + index">
																<td><strong>Giá sau điều chỉnh</strong></td>
																<td></td>
																<td><strong>{{formatNumber(adjustPriceData['so_phong_wc'][0])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['so_phong_wc'][1])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['so_phong_wc'][2])}}</strong></td>
															</tr>

															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_ngu' && comparison_factor_appraise.status === 1)" :key="'sophongngu' + index">
																<td rowspan="4">
																	<div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('so_phong_ngu')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
																</td>
																<td><strong>Số phòng ngủ</strong></td>
																<td v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_ngu' && comparison_factor_appraise.status === 1)" :key="'sophongngu1' + index">
																	{{formatSentenceCase(comparison_factor_appraise.apartment_title)}}
																</td>
																<td v-for="(asset, index) in apartment.assets_general" :key="'assetSoPhongNgu' + index">
																	<span v-for="(comparison_factor, indexItem) in apartment.comparison_factor[index].comparison_factor"  :key="'sophongngu2' +indexItem">
																		<span v-if="comparison_factor.type === 'so_phong_ngu'">
																			{{formatSentenceCase(comparison_factor.asset_title)}}
																		</span>
																	</span>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_ngu' && comparison_factor_appraise.status === 1)" :key="'sophongngu3' + index">
																<td>Tỷ lệ điều chỉnh</td>
																<td></td>
																<td v-for="(asset, indexAsset) in apartment.assets_general" :key="'inputSoPhongNgu' + indexAsset">
																	<div v-for="(comparison_factor, index) in apartment.comparison_factor[indexAsset].comparison_factor"  :key="'sophongngu4' +index">
																		<div v-if="comparison_factor.type === 'so_phong_ngu'">
																			<InputPercentNegative
																				:disabled="!isEditStatus"
																				class="label-none input_center"
																				v-model="comparison_factor.adjust_percent"
																				vid="number_legal"
																				:text_center="true"
																				@change="changeLegalRate($event, indexAsset, comparison_factor.type)"
																			/>
																		</div>
																	</div>
																</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_ngu' && comparison_factor_appraise.status === 1)" :key="'sophongngu5' + index">
																<td>Mức điều chỉnh</td>
																<td></td>
																<td>{{formatNumber(parseFloat(pricePn1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(pricePn2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(pricePn3).toFixed(0))}}</td>
															</tr>
															<tr v-for="(comparison_factor_appraise, index) in apartment.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'so_phong_ngu' && comparison_factor_appraise.status === 1)" :key="'sophongngu6' + index">
																<td><strong>Giá sau điều chỉnh</strong></td>
																<td></td>
																<td><strong>{{formatNumber(adjustPriceData['so_phong_ngu'][0])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['so_phong_ngu'][1])}}</strong></td>
																<td><strong>{{formatNumber(adjustPriceData['so_phong_ngu'][2])}}</strong></td>
															</tr>

															<tr v-if="showOtherFactor && data_other_comparison && data_other_comparison.length > 0"
																		v-for="(data_appraise, index) in data_other_comparison || []"
																		:key="'yeu_to_khac' + index">
																	<td style="padding: unset" colspan="6">
																	<table class="table_of_other_comparison color_content">
																		<tr style="width: 100% !important">
																			<td rowspan="4">
																				<div  v-if="isEditStatus" class="btn-delete" type="button" @click="handleDialogDeleteComparision(index, data_appraise.other_factor_asset)"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
																			</td>
																			<td class="td_other_appraise">
																				<InputText
																					class="inputLabel inputLabelCompare"
																					v-model="data_appraise.name"
																					vid="description"
																					styleInput="text-align:center;font-weight:bold"
																				@change="handleChangeNameFactor($event, data_appraise.other_factor_asset)"
																			/>
																			</td>
																			<td class="td_other_asset_title">
																				<InputText
																					v-model="data_appraise.apartment_title"
																					vid="description"
																					styleInput="text-align:center"
																				@change="handleChangeTitleAppraise($event, data_appraise.other_factor_asset)"
																				/>
																			</td>
																			<td class="td_other_asset_title" v-for="(data_asset, indexItem) in data_appraise.other_factor_asset" :key="'yeutokhac2' +indexItem">
																				<div >
																					<InputText

																						v-model="data_asset.asset_title"
																						vid="description"
																						styleInput="text-align:center"
																						@change="handleChangeTitleAsset($event, data_asset)"
																					/>
																				</div>
																			</td>
																		</tr>
																		<tr v-if="showOtherFactor" >
																			<td class="td_other_appraise">Tỷ lệ điều chỉnh</td>
																			<td class="td_other_asset_title"></td>
																			<td class="td_other_asset_title" v-for="(rate_asset, index) in data_appraise.other_factor_asset" :key="'yeutokhac4' +index">
																				<div>
																					<InputNumberNegative
																						:disabledInput="!isEditStatus"
																						class="label-none input_number_center"
																						v-model="rate_asset.adjust_percent"
																						vid="number_legal"
																						label="Tỷ lệ"
																						:min="-100"
																						:sufix="true"
																						:percent="true"
																						:text_center="true"
																						@change="changeOtherRate($event, rate_asset)"
																					/>
																				</div>
																			</td>
																		</tr>
																		<tr v-if="showOtherFactor">
																			<td class="td_other_appraise">Mức điều chỉnh</td>
																			<td class="td_other_asset_title"></td>
																			<td class="td_other_asset_title" v-for="(price_other, index) in price_other_comparison ? price_other_comparison[index] : []" :key="'yeutokhacprice' +index">
																				{{formatNumber(parseFloat(price_other.indication_price_asset).toFixed(0)) || 0}}
																			</td>
																		</tr>
																		<tr v-if="showOtherFactor">
																			<td class="td_other_appraise">Giá sau điều chỉnh</td>
																			<td class="td_other_asset_title"></td>
																			<td class="td_other_asset_title" v-for="(price_other, index) in price_other_comparison ? price_other_comparison[index] : []" :key="'yeutokhacprice2' +index">
																				{{formatNumber(parseFloat(price_other.adjust_price_asset).toFixed(0)) || 0}}
																			</td>
																		</tr>
																	</table>
																	</td>
															</tr>

															<tr class="other_button_container" >
																<td class="td_none"></td>
																<td class="td_none" colspan="5">
																	<div class="container_btn_add">
																		<button v-if="isEditStatus" class="btn text-warning btn-ghost other_button" type="button" @click="handleAddOtherFactor" >+ Thêm yếu tố khác</button>
																	</div>
																</td>
															</tr>

															<tr>
																<td>2</td>
																<td colspan="2">Mức giá chỉ dẫn (đồng/m<sup>2</sup>)</td>
																<td>{{formatNumber(parseFloat(mgcd1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(mgcd2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(mgcd3).toFixed(0))}}</td>
															</tr>
															<tr>
																<td>3</td>
																<td colspan="2">Mức giá trung bình của các mức giá chỉ dẫn (đồng/m<sup>2</sup>)</td>
																<td align="center" colspan="3">{{formatNumber(parseFloat(mgtb).toFixed(0))}}</td>
															</tr>

															<tr>
																<td>4</td>
																<td colspan="2" :class=" (mgcl1 > 15 || mgcl2 > 15 || mgcl3 > 15)  || (mgcl1 < -15 || mgcl2 < -15 || mgcl3 < -15) ? 'text-danger' : ''">
																	Mức độ chênh lệch với mức giá trung bình của các mức giá chỉ dẫn (%)
																</td>
																<td :class="mgcl1 > 15 || mgcl1 < -15 ? 'text-danger' : ''" >{{mgcl1 + '%'}}</td>
																<td :class="mgcl2 > 15 || mgcl2 < -15 ? 'text-danger' : ''">{{mgcl2 + '%'}}</td>
																<td :class="mgcl3 > 15 || mgcl3 < -15 ? 'text-danger' : ''">{{mgcl3 + '%'}}</td>
															</tr>
															<tr>
																<td>5</td>
																<td colspan="2">Tổng giá trị điều chỉnh gộp</td>
																<td>{{formatNumber(parseFloat(tldcg1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(tldcg2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(tldcg3).toFixed(0))}}</td>
															</tr>
															<tr >
																<td>6</td>
																<td colspan="2">Tổng số lần điều chỉnh (lần)</td>
																<td>{{formatNumber(parseFloat(comparisonFactorChange1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(comparisonFactorChange2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(comparisonFactorChange3).toFixed(0))}}</td>
															</tr>
															<tr>
																<td>7</td>
																<td colspan="2">Biên độ điều chỉnh (%)</td>
																<td v-for="(asset, index_asset) in apartment.assets_general" :key="'asset_area_adjusted' + index_asset" >
																	<div v-for="(percent, index) in area_adjusted" :key="'percent_adjust' + index" >
																		<span v-if="percent.id === asset.id">
																			{{`${checkMin(area_adjusted[asset.id])} - ${checkMax(area_adjusted[asset.id])}`}}
																		</span>
																	</div>
																</td>
															</tr>
															<tr>
																<td>8</td>
																<td colspan="2">Tổng giá trị điều chỉnh thuần(đ/m<sup>2</sup>)</td>
																<td>{{formatNumber(parseFloat(tldc1).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(tldc2).toFixed(0))}}</td>
																<td>{{formatNumber(parseFloat(tldc3).toFixed(0))}}</td>
															</tr>
															<tr>
																<td>9</td>
																<td colspan="2">
																	<strong>Thống nhất mức giá chỉ dẫn</strong>
																</td>
																<td colspan="3" align="center"><strong>{{formatNumber(parseFloat(mgtn).toFixed(0))}}</strong></td>
															</tr>
															<tr>
																<td>10</td>
																<td colspan="2">
																		<div class="d-flex col-12 col-lg-12 container_round">
																				<p class="title_round">Làm tròn</p>
																				<div style="width: 70px">
																					<InputNumberFormat
																						:disabledInput="!isEditStatus"
																						v-model="round_total"
																						vid="round_total"
																						:max="99999999"
																						:min="-99999999"
																						@change="changeRoundTotal($event)"
																					/>
																				</div>
																		</div>
																</td>
																<td colspan="3" align="center">{{formatNumber(formatCurrent(parseFloat(mgtn).toFixed(0)))}}đ</td>
															</tr>
													</tbody>
												</table>
												<div class="w-100 mt-4">
												<div class="d-flex align-items-center sub_header_title">
														<span class="label">Bảng tổng hợp</span>
												</div>
												<div class="main-wrapper mb-2">
													<div class="responsive-table">
														<table class="table_result_adjust">
															<thead>
																<tr>
																		<th>Tên tài sản</th>
																		<th>Diện tích</th>
																		<th>Đơn giá (đ)</th>
																		<th>Thành tiền</th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td style="width:28%">Giá trị căn hộ</td>
																	<td>{{data.apartment_asset_properties ? formatNumber(data.apartment_asset_properties.area) : ''}}</td>
																	<td>{{formatNumber(formatCurrent(parseFloat(mgtn).toFixed(0)))}}</td>
																	<td>{{formatNumber(parseFloat(showPriceApartment()).toFixed(0))}}đ</td>
																</tr>
																<tr>
																	<td colspan="1"><strong>Tổng cộng</strong></td>
																	<td><strong>{{data.apartment_asset_properties ? formatNumber(data.apartment_asset_properties.area) : ''}}</strong></td>
																	<td></td>
																	<td><strong>{{formatNumber(parseFloat(showPriceApartment()).toFixed(0))}}đ</strong></td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
												</div>
											</div>
										</div>

								</div>
								<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
									<div class="d-md-flex d-block">
										<button  @click="onCancel" class="btn btn-white text-nowrap" >
											<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Thoát
										</button>
										<button v-if="isEditStatus" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="handleSaveTab2">
											<img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
										</button>
									</div>
								</div>
							</div>
						</TabItem>

						<TabItem class="item_4" name="Tài sản khác">
							<ModalStep5TabOtherProperty
							:data="form"
							:idData="idData"
							:status="status"
							:isEditStatus="isEditStatus"
							@updateTotalOtherAsset="updateTotalOtherAsset"  />
						</TabItem>
						<TabItem class="item_5" name="Tổng hợp kết quả">
							<ModalStep5Summarize
							:key="key_render_5"
							:data="form"
							:total_price="total_price"
							:apartment_total_price="apartment_total_price"
							:other_asset_price="other_asset_price"
							:apartment_asset_price="apartment_asset_price"
							:apartment_round_total="apartment_round_total"
							:idData="idData"
							:status="status"
							:isEditStatus="isEditStatus"
							/>
						</TabItem>
					</Tabs>
				</div>
				<ModalDelete
					v-if="openMdodalDeleteDefault"
					@cancel="openMdodalDeleteDefault = false"
					@action="actionDeleteComparisionDefault"
				/>
				<ModalDelete
					v-if="openModalDelete"
					@cancel="openModalDelete = false"
					@action="handleDeleteOtherFactor"
				/>
				<ModalNotificationAppraisal
					v-if="showWarningSave"
					@cancel="showWarningSave = false"
					v-bind:notification="'Độ chênh lệch của mức giá trung bình với các mức giá chỉ dẫn đang lớn hơn 15% không phù hợp tiêu chuẩn thẩm định giá.\n Bạn có muốn lưu?'"
					@action="handleSaveContinue"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import InputNumberFormat from '@/components/Form/InputNumber'
import { AlertCircleIcon } from 'vue-feather-icons'
import InputPercent from '@/components/Form/InputPercent'
import InputNumberNew from '@/components/Form/InputNumberNew'
import InputCurrency from '@/components/Form/InputCurrency'
import InputCategoryBoolean from '@/components/Form/InputCategoryBoolean'
import InputArea from '@/components/Form/InputArea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputNumberNegative from '@/components/Form/InputNumberNegative'
import InputPercentNegative from '@/components/Form/InputPercentNegative'
import InputSwitch from '@/components/Form/InputSwitch'
import InputLengthArea from '@/components/Form/InputLengthArea'
import { Tabs, TabItem } from 'vue-material-tabs'
import InputSwitchLayerCuting from '@/components/Form/InputSwitchLayerCuting'
import ModalDelete from '@/components/Modal/ModalDelete'
import CertificateAsset from '@/models/CertificateAsset'
import ModalStep5TabOtherProperty from './modals/ModalStep5TabOtherProperty'
import ModalStep5Summarize from './modals/ModalStep5Summarize'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import { BAlert } from 'bootstrap-vue'
import Vue from 'vue'
import Icon from 'buefy'

Vue.use(Icon)

export default {
	name: 'apartment_step5_detail',
	props: ['data', 'idData', 'status', 'isEditStatus', 'jsonConfig'],
	components: {
		Tabs,
		TabItem,
		InputCategory,
		ModalDelete,
		InputText,
		InputNumberNegative,
		InputPercentNegative,
		InputSwitch,
		InputLengthArea,
		InputArea,
		InputDatePicker,
		InputCategoryBoolean,
		InputCurrency,
		InputNumberNew,
		InputPercent,
		BAlert,
		AlertCircleIcon,
		InputSwitchLayerCuting,
		ModalStep5TabOtherProperty,
		ModalStep5Summarize,
		InputNumberFormat,
		ModalNotificationAppraisal
	},
	computed: {},
	data () {
		return {
			theme: {
				navItem: '#000000',
				navActiveItem: '#007EC6',
				slider: '#007EC6',
				arrow: '#000000'
			},
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			key_render_5: 900000,
			key_render_1: 7000000,
			key_render_around: 600023,
			showDetailContruction: true,
			apartment: {},
			typeComparision: '',
			openMdodalDeleteDefault: false,
			indexDeleteComparision: null,
			dataDeleteComparision: {},
			openModalDelete: false,
			otherFactor: 'Yếu tố khác',
			showOtherFactor: false,
			totalPriceEstimate1: 0,
			totalPriceEstimate2: 0,
			totalPriceEstimate3: 0,
			dgcc1: 0,
			dgcc2: 0,
			dgcc3: 0,
			totalPricePL1: 0,
			totalPricePL2: 0,
			totalPricePL3: 0,
			pricePn1: 0,
			pricePn2: 0,
			pricePn3: 0,
			priceLch1: 0,
			priceLch2: 0,
			priceLch3: 0,
			priceDt1: 0,
			priceDt2: 0,
			priceDt3: 0,
			priceSt1: 0,
			priceSt2: 0,
			priceSt3: 0,
			pricePWC1: 0,
			pricePWC2: 0,
			pricePWC3: 0,
			mgcd1: 0,
			mgcd2: 0,
			mgcd3: 0,
			mgcdMin: 0,
			mgcdMax: 0,
			mgtb: 0,
			mgtn: 0,
			tldc1: 0,
			tldc2: 0,
			tldc3: 0,
			pricePl1: 0,
			pricePl2: 0,
			pricePl3: 0,
			mgcl1: 0,
			mgcl2: 0,
			mgcl3: 0,
			comparisonFactorChange1: 0,
			comparisonFactorChange2: 0,
			comparisonFactorChange3: 0,
			priceYtk1: 0,
			priceYtk2: 0,
			priceYtk3: 0,
			showError: false,
			other_comparison: [],
			data_other_comparison: [],
			price_other_comparison: [],
			delete_other_comparison: [],
			otherIndex: 0,
			area_adjusted: {},
			round_total: 0,
			showWarningSave: false,
			dataTab2: {},
			apartment_total_price: 0,
			other_asset_price: 0,
			apartment_asset_price: 0,
			apartment_round_total: 0,
			total_price: 0,
			isSubmit: false,
			adjustPriceData: {}
		}
	},
	mounted () {
		if (this.form && this.form.assets_general) {
			this.getOtherComparison()
			this.calculation(this.form)
			this.getData()
			this.getSummarizedPrice(this.form)
		}
	},
	beforeUpdate () {
	},
	methods: {
		roundPrice (value, roundPrice) {
			if (!value) {
				return value
			}
			if (roundPrice && roundPrice > 0 && roundPrice <= 7) {
				let round = Math.pow(10, roundPrice)
				return Math.ceil(value / round) * round
			} else if (roundPrice && roundPrice < 0 && roundPrice >= -7) {
				let round = Math.pow(10, Math.abs(roundPrice))
				return Math.floor(value / round) * round
			} else return parseInt(Number(value).toFixed(0))
		},
		converArrayToString (array) {
			let text = ''
			if (array && array.length > 0) {
				array.forEach(item => {
					text += item + ', '
				})
				return text
			}
		},
		showUtility (apartment_specification) {
			if (apartment_specification && apartment_specification.utility_description && apartment_specification.utility_description.length > 0) {
				return this.converArrayToString(apartment_specification.utility_description)
			} else return '-'
		},
		getSummarizedPrice (data) {
			if (data.price && data.price.length > 0) {
				data.price.forEach(item => {
					if (item.slug === 'apartment_total_price') {
						this.apartment_total_price = item.value
					} else if (item.slug === 'other_asset_price') {
						this.other_asset_price = item.value
					} else if (item.slug === 'apartment_asset_price') {
						this.apartment_asset_price = item.value
					} else if (item.slug === 'apartment_round_total') {
						this.apartment_round_total = item.value
					} else if (item.slug === 'total_price') {
						this.total_price = item.value
					} else if (item.slug === 'round_total') {
						this.round_total = item.value
					}
				})
			}
		},
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		onCancel () {
			return this.$router.push({name: 'certification_asset.index'})
		},
		// ---------------------------------------------- TAB_1 --------------------------------------------------------------------//
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		checkMin (data) {
			if (data.min > data.max && data.max !== 0) {
				return `${data.max}%`
			} else if ((data.min === 0 && data.max !== 0) || data.min === data.max) {
				return `${data.max}%`
			} else if (data.max === 0 && data.min !== 0) {
				return `${data.min}%`
			} else return `${data.min}%`
		},
		checkMax (data) {
			if (data.max < data.min) {
				return `${data.min}%`
			} else return `${data.max}%`
		},
		remainingBuildingPrice (tangible_assets) {
			if (tangible_assets && tangible_assets.unit_price_m2 && tangible_assets.remaining_quality && tangible_assets.total_construction_base) {
				return this.formatNumber((tangible_assets.unit_price_m2 * tangible_assets.total_construction_base) * tangible_assets.remaining_quality / 100)
			} else return '-'
		},
		changePercentSaleRating (event, index) {
			if (event) {
				this.form.apartment_adapter[index].percent = event
			}
			this.calculation(this.form)
		},
		checkDataPriceUBND (asset, pricePurposeLands) {
			let empty = true
			pricePurposeLands.forEach(item => {
				if (item.asset_general_id === asset.id) {
					empty = false
				}
			})
			return empty
		},
		getData () {
			// set config render data
			this.apartment = {}
			let appraise = JSON.parse(JSON.stringify(this.form))
			let asset_generals = []
			if (appraise.assets_general) {
				appraise.comparison_factor.forEach((data, index) => {
					if (data.type === 'yeu_to_khac' && data.asset_title === '' && data.apartment_title === '') {
						appraise.comparison_factor[index].asset_title = 'Không biết'
						appraise.comparison_factor[index].apartment_title = 'Không biết'
					}
				})
				appraise.assets_general.forEach(assets_general => {
					// assets_general.adjust_percent = 100 + +assets_general.adjust_percent
					const comparison_factor_TSSS = appraise.comparison_factor.filter(comparison => comparison.asset_general_id === assets_general.id)
					asset_generals.push({
						id: assets_general.id,
						comparison_factor: comparison_factor_TSSS
					})
				})
				this.apartment = {
					id: appraise.id,
					comparison_factor: asset_generals,
					assets_general: appraise.assets_general
				}
				// console.log(this.apartment, 'apartment')
			}
		},

		async getOtherComparison () {
			await this.form.comparison_factor.forEach(item => {
				if (item.type === 'yeu_to_khac') {
					this.other_comparison.push(item)
				}
			})
			// show data
			const map = new Map()
			this.data_other_comparison = []
			let indexTem = {}
			if (this.other_comparison.length > 0) {
				this.showOtherFactor = true
			}
			await this.other_comparison.forEach(item => {
				if (!map.has(item.position)) {
					map.set(item.position, true)
					indexTem['id_check'] = item.position
					const comparison_other = this.other_comparison.filter(data => data.position === indexTem['id_check'])
					// tạo data ảo hiển thị
					this.data_other_comparison.push({
						apartment_title: item.apartment_title,
						name: item.name,
						other_factor_asset: comparison_other
					})
					//
				}
			})
			// set otherIndex
			let positionTem = []
			await this.other_comparison.map(data => { positionTem.push(data.position) })
			let max = Math.max(...positionTem)
			if (positionTem.length === 0 || max === -Infinity || max === Infinity || !max) {
				this.otherIndex = 0
			} else this.otherIndex = max
			await this.calculation(this.form)
		},

		handleSaveTab1 (tab1) {
			const dataSave = []
			const otherDataSave = null
			const dataDelete = null
			const round_total = null
			const apartment_adapter = this.form.apartment_adapter
			const payloadData = {
				comparison_factor: dataSave,
				other_comparison: otherDataSave,
				delete_other_comparison: dataDelete,
				round_total: +round_total,
				apartment_adapter: apartment_adapter
			}
			this.handleSaveSummarization(payloadData)
		},
		async handleSaveSummarization (payloadData) {
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
			const res = await CertificateAsset.submitApartmentStep5(payloadData, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu bảng tổng hợp thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.getSummarizedPrice(res.data)
				this.key_render_5 += 1
				this.$emit('updateDataStep7')
				this.isSubmit = false
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
				this.isSubmit = false
			}
		},
		// ---------------------------------------------- TAB_2 --------------------------------------------------------------------//
		showPriceApartment () {
			if (this.form.apartment_asset_properties) {
				return +this.formatCurrent(parseFloat(this.mgtn).toFixed(0)) * this.form.apartment_asset_properties.area
			} else return 0
		},
		formatCurrent (value) {
			if (this.round_total && this.round_total > 0 && this.round_total <= 7) {
				let round = Math.pow(10, this.round_total)
				return Math.ceil(value / round) * round
			} else if (this.round_total && this.round_total < 0 && this.round_total >= -7) {
				let round = Math.pow(10, Math.abs(this.round_total))
				return Math.floor(value / round) * round
			} else return value
		},
		getArrayMin (arr) {
			let arrTemp = []
			arr.forEach(item => { item.adjust_percent = Math.abs(item.adjust_percent) })
			arrTemp = arr.filter(item => item.adjust_percent !== 0)
			if (arrTemp.length > 0) {
				const min = Math.min(...arrTemp.map(item => item.adjust_percent))
				return min
			} else return 0
		},
		getArrayMax (arr) {
			let arrTemp = []
			arr.forEach(item => { item.adjust_percent = Math.abs(item.adjust_percent) })
			arrTemp = arr.filter(item => item.adjust_percent !== 0)
			if (arrTemp.length > 0) {
				const max = Math.max(...arr.map(item => item.adjust_percent))
				return max
			} else return 0
		},
		async dialogDeleteComparisionDefault (type) {
			this.openMdodalDeleteDefault = true
			this.typeComparision = type
		},
		async actionDeleteComparisionDefault () {
			let tempArrayComparison = []
			await this.apartment.comparison_factor.forEach(data => {
				data.comparison_factor.forEach(item => {
					if (item.type === this.typeComparision) {
						item.status = 0
						item.adjust_percent = 0
					}
					tempArrayComparison.push(item)
				})
			})
			this.form.comparison_factor = await tempArrayComparison
			await this.calculation(this.form)
		},
		changeLegalRate (e, indexAsset, type) {
			let tempArrayComparison = []
			let factor = this.apartment.comparison_factor[indexAsset].comparison_factor.find(i => i.type === type)
			if (factor) {
				if (e) {
					factor.percent = e
				} else {
					factor.percent = 0
				}
			}
			this.apartment.comparison_factor.forEach(data => {
				data.comparison_factor.forEach(item => {
					tempArrayComparison.push(item)
				})
			})
			this.form.comparison_factor = tempArrayComparison
			this.calculation(this.form)
			this.key_render_1 += 1
			this.key_render_around += 1
		},
		async handleAddOtherFactor () {
			const data = this.form
			this.data_other_comparison = []
			let indexTem = {}
			this.otherIndex++
			const map = new Map()
			await this.form.assets_general.forEach(asset => {
				// tạo data gửi
				this.other_comparison.push({
					adjust_percent: 0,
					apartment_asset_id: data.id,
					apartment_title: 'Không biết',
					asset_general_id: asset.id,
					asset_title: 'Không biết',
					description: '',
					position: this.otherIndex,
					name: `Yếu tố khác`,
					status: 1,
					type: 'yeu_to_khac'
				})
			})

			// show data
			if (this.other_comparison.length > 0) {
				this.showOtherFactor = true
			}
			await this.other_comparison.forEach(item => {
				if (!map.has(item.position)) {
					map.set(item.position, true)
					indexTem['id_check'] = item.position
					const comparison_other = this.other_comparison.filter(data => data.position === indexTem['id_check'])
					// tạo data ảo hiển thị
					this.data_other_comparison.push({
						apartment_title: item.apartment_title,
						name: item.name,
						other_factor_asset: comparison_other
					})
					//
				}
			})
			await this.$toast.open({
				message: 'Thêm yếu tố khác thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
			await this.calculation(this.form)
		},
		handleDialogDeleteComparision (index, data) {
			this.indexDeleteComparision = index
			this.dataDeleteComparision = data
			this.openModalDelete = true
		},
		handleDeleteOtherFactor () {
			let index = this.indexDeleteComparision
			let data = this.dataDeleteComparision
			if (data && data.length > 0) {
				// data-delete
				const delete_comparison = this.other_comparison.filter(item => item.position === data[0].position)
				this.delete_other_comparison.push(...delete_comparison)
				// data-remain
				const other_comparison = this.other_comparison.filter(item => item.position !== data[0].position)
				this.other_comparison = other_comparison
			}
			this.data_other_comparison.splice(index, 1)
			this.calculation(this.form)
		},
		handleChangeTitleAsset (event, data) {
			if (data && data.length > 0) {
				this.other_comparison.forEach(item => {
					if (item.position === data.position && item.asset_general_id === data.asset_general_id) {
						item.asset_title = event
					}
				})
			}
		},
		handleChangeTitleAppraise (event, data) {
			this.other_comparison.forEach(item => {
				if (data && data.length > 0 && item.position === data[0].position) {
					item.apartment_title = event
				}
			})
		},
		handleChangeNameFactor (event, data) {
			this.other_comparison.forEach(item => {
				if (data && data.length > 0 && item.position === data[0].position) {
					item.name = event
				}
			})
		},
		changeOtherRate (event, data) {
			if (event) {
				this.other_comparison.forEach(item => {
					if (item.position === data.position && item.asset_general_id === data.asset_general_id) {
						item.adjust_percent = event
					}
				})
			} else {
				this.other_comparison.forEach(item => {
					if (item.position === data.position && item.asset_general_id === data.asset_general_id) {
						item.adjust_percent = 0
					}
				})
			}
			this.calculation(this.form)
		},
		// lam tron dat co so
		changeRoundTotal (event) {
			if (event || event === 0) {
				this.round_total = parseFloat(event).toFixed(0)
			} else {
				this.round_total = 0
			}
			this.calculation(this.form)
			this.key_render_around += 1
		},
		handleSaveTab2 () {
			const dataSave = []
			const apartment_adapter = this.form.apartment_adapter
			const otherDataSave = this.other_comparison
			const dataDelete = this.delete_other_comparison
			const round_total = this.round_total

			if (typeof this.apartment !== 'undefined') {
				this.apartment.comparison_factor.forEach(comparison => {
					comparison.comparison_factor.forEach(data => {
						dataSave.push(data)
					})
				})
			}
			const payloadData = {
				apartment_adapter: apartment_adapter,
				comparison_factor: dataSave,
				other_comparison: otherDataSave,
				delete_other_comparison: dataDelete,
				round_total: +round_total
			}
			if (round_total < -7 || round_total > 7) {
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (Math.abs(this.mgcl1) > 15 || Math.abs(this.mgcl2) > 15 || Math.abs(this.mgcl3) > 15) {
				this.showWarningSave = true
				this.dataTab2 = payloadData
			} else this.handleSaveAdjustPlan(payloadData)
		},
		handleSaveContinue () {
			this.handleSaveAdjustPlan(this.dataTab2)
		},
		async handleSaveAdjustPlan (payloadData) {
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
			const res = await CertificateAsset.submitApartmentStep5(payloadData, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu bảng điều chỉnh QSDĐ thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.getSummarizedPrice(res.data)
				let comparison_factor_resfresh = []
				await this.form.assets_general.forEach(assets_general => {
					const comparison_factor_TSSS = res.data.comparison_factor.filter(comparison => comparison.asset_general_id === assets_general.id)
					comparison_factor_resfresh.push({
						id: assets_general.id,
						comparison_factor: comparison_factor_TSSS
					})
				})
				this.delete_other_comparison = []
				this.other_comparison = []
				this.apartment.comparison_factor = comparison_factor_resfresh
				this.form.comparison_factor = res.data.comparison_factor
				this.getOtherComparison()
				this.key_render_5 += 1
				this.isSubmit = false
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
				this.isSubmit = false
			}
		},

		// ------------------------------------------------- TAB_3 ---------------------------------------------------------------
		updateTotalOtherAsset (data) {
			this.getSummarizedPrice(data)
			this.key_render_5 += 1
		},
		updateAssetPrice (data) {
			this.getSummarizedPrice(data)
			this.key_render_5 += 1
		},
		// ------------------------------------------------- function calculation ---------------------------------------------------------------
		calculation (asset) {
			let arrayPriceTem = []
			// thông tin TSSS
			let asset1 = (typeof asset.assets_general[0] !== 'undefined') ? asset.assets_general[0] : null
			let asset2 = (typeof asset.assets_general[1] !== 'undefined') ? asset.assets_general[1] : null
			let asset3 = (typeof asset.assets_general[2] !== 'undefined') ? asset.assets_general[2] : null

			// tỉ lệ giá rao bán
			let asset_percent1 = (typeof asset.apartment_adapter[0] !== 'undefined') ? asset.apartment_adapter[0].percent : null
			let asset_percent2 = (typeof asset.apartment_adapter[1] !== 'undefined') ? asset.apartment_adapter[1].percent : null
			let asset_percent3 = (typeof asset.apartment_adapter[2] !== 'undefined') ? asset.apartment_adapter[2].percent : null

			// tính Tổng giá trị tài sản ước tính
			this.totalPriceEstimate1 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent1 * asset1.total_amount) / 100 : 0)
			this.totalPriceEstimate2 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent2 * asset2.total_amount) / 100 : 0)
			this.totalPriceEstimate3 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent3 * asset3.total_amount) / 100 : 0)

			// tính đơn giá chung cư
			this.dgcc1 = asset1.room_details && asset1.room_details.length > 0 ? this.totalPriceEstimate1 / asset1.room_details[0].area : 0
			this.dgcc2 = asset2.room_details && asset2.room_details.length > 0 ? this.totalPriceEstimate2 / asset2.room_details[0].area : 0
			this.dgcc3 = asset3.room_details && asset3.room_details.length > 0 ? this.totalPriceEstimate3 / asset3.room_details[0].area : 0

			let comparisonFactor1 = []
			let comparisonFactor2 = []
			let comparisonFactor3 = []

			this.totalPricePL1 = 0
			this.totalPricePL2 = 0
			this.totalPricePL3 = 0
			// lấy YTSS của TSSS
			asset.comparison_factor.forEach((comparisonFactor, index) => {
				if (asset1 && (comparisonFactor.asset_general_id === asset1.id)) {
					comparisonFactor1[comparisonFactor.type] = comparisonFactor
				}
				if (asset2 && (comparisonFactor.asset_general_id === asset2.id)) {
					comparisonFactor2[comparisonFactor.type] = comparisonFactor
				}
				if (asset3 && (comparisonFactor.asset_general_id === asset3.id)) {
					comparisonFactor3[comparisonFactor.type] = comparisonFactor
				}
			})
			this.comparisonFactorChange1 = 0
			this.comparisonFactorChange2 = 0
			this.comparisonFactorChange3 = 0

			// YTSS
			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.pricePl1 = 0
			this.pricePl2 = 0
			this.pricePl3 = 0
			if ((typeof comparisonFactor1['phap_ly'] !== 'undefined') && comparisonFactor1['phap_ly'].status === 1) {
				let percentPl1 = (typeof comparisonFactor1['phap_ly'].adjust_percent !== 'undefined') ? comparisonFactor1['phap_ly'].adjust_percent : 0
				let percentPl2 = (typeof comparisonFactor2['phap_ly'].adjust_percent !== 'undefined') ? comparisonFactor2['phap_ly'].adjust_percent : 0
				let percentPl3 = (typeof comparisonFactor3['phap_ly'].adjust_percent !== 'undefined') ? comparisonFactor3['phap_ly'].adjust_percent : 0
				// mức điều chỉnh của yếu tố PHÁP LÝ
				this.pricePl1 = percentPl1 * this.dgcc1 / 100
				this.pricePl2 = percentPl2 * this.dgcc2 / 100
				this.pricePl3 = percentPl3 * this.dgcc3 / 100

				this.totalPricePL1 = this.dgcc1 * (1 + percentPl1 / 100) // giá sau điều chỉnh
				this.totalPricePL2 = this.dgcc2 * (1 + percentPl2 / 100) // giá sau điều chỉnh
				this.totalPricePL3 = this.dgcc3 * (1 + percentPl3 / 100) // giá sau điều chỉnh

				arrayPriceTem.push(this.totalPricePL1)
				arrayPriceTem.push(this.totalPricePL2)
				arrayPriceTem.push(this.totalPricePL3)

				this.comparisonFactorChange1 += (percentPl1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentPl2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentPl3 !== 0) ? 1 : 0
			}
			this.adjustPriceData = {}
			let adjustPrice1 = this.roundPrice(this.totalPricePL1, 0)
			let adjustPrice2 = this.roundPrice(this.totalPricePL2, 0)
			let adjustPrice3 = this.roundPrice(this.totalPricePL3, 0)
			this.adjustPriceData['phap_ly'] = [adjustPrice1, adjustPrice2, adjustPrice3]

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceLch1 = 0
			this.priceLch2 = 0
			this.priceLch3 = 0
			if ((typeof comparisonFactor1['loai_can'] !== 'undefined') && comparisonFactor1['loai_can'].status === 1) {
				let percentLch1 = +comparisonFactor1['loai_can'].adjust_percent || 0
				let percentLch2 = +comparisonFactor2['loai_can'].adjust_percent || 0
				let percentLch3 = +comparisonFactor3['loai_can'].adjust_percent || 0

				// mức điều chỉnh của yếu tố LOẠI CĂN HỘ
				let price1 = this.roundPrice(percentLch1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentLch2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentLch3 * this.totalPricePL3 / 100, 0)
				this.priceLch1 = price1
				this.priceLch2 = price2
				this.priceLch3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['loai_can'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentLch1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentLch2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentLch3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceDt1 = 0
			this.priceDt2 = 0
			this.priceDt3 = 0
			if ((typeof comparisonFactor1['dien_tich'] !== 'undefined') && comparisonFactor1['dien_tich'].status === 1) {
				let percentDt1 = +comparisonFactor1['dien_tich'].adjust_percent || 0
				let percentDt2 = +comparisonFactor2['dien_tich'].adjust_percent || 0
				let percentDt3 = +comparisonFactor3['dien_tich'].adjust_percent || 0

				// mức điều chỉnh của yếu tố Diện Tích
				let price1 = this.roundPrice(percentDt1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentDt2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentDt3 * this.totalPricePL3 / 100, 0)
				this.priceDt1 = price1
				this.priceDt2 = price2
				this.priceDt3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['dien_tich'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentDt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDt3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceSt1 = 0
			this.priceSt2 = 0
			this.priceSt3 = 0
			if ((typeof comparisonFactor1['tang'] !== 'undefined') && comparisonFactor1['tang'].status === 1) {
				let percentSt1 = +comparisonFactor1['tang'].adjust_percent || 0
				let percentSt2 = +comparisonFactor2['tang'].adjust_percent || 0
				let percentSt3 = +comparisonFactor3['tang'].adjust_percent || 0

				// mức điều chỉnh của yếu tố SỐ TẦNG
				let price1 = this.roundPrice(percentSt1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentSt2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentSt3 * this.totalPricePL3 / 100, 0)
				this.priceSt1 = price1
				this.priceSt2 = price2
				this.priceSt3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['tang'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentSt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentSt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentSt3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.pricePWC1 = 0
			this.pricePWC2 = 0
			this.pricePWC3 = 0
			if ((typeof comparisonFactor1['so_phong_wc'] !== 'undefined') && comparisonFactor1['so_phong_wc'].status === 1) {
				let percentPWC1 = +comparisonFactor1['so_phong_wc'].adjust_percent || 0
				let percentPWC2 = +comparisonFactor2['so_phong_wc'].adjust_percent || 0
				let percentPWC3 = +comparisonFactor3['so_phong_wc'].adjust_percent || 0

				// mức điều chỉnh của yếu tố WC
				let price1 = this.roundPrice(percentPWC1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentPWC2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentPWC3 * this.totalPricePL3 / 100, 0)
				this.pricePWC1 = price1
				this.pricePWC2 = price2
				this.pricePWC3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['so_phong_wc'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentPWC1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentPWC2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentPWC3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.pricePn1 = 0
			this.pricePn2 = 0
			this.pricePn3 = 0
			if ((typeof comparisonFactor1['so_phong_ngu'] !== 'undefined') && comparisonFactor1['so_phong_ngu'].status === 1) {
				let percentPn1 = +comparisonFactor1['so_phong_ngu'].adjust_percent || 0
				let percentPn2 = +comparisonFactor2['so_phong_ngu'].adjust_percent || 0
				let percentPn3 = +comparisonFactor3['so_phong_ngu'].adjust_percent || 0

				// mức điều chỉnh của yếu tố SỐ PHÒNG NGỦ
				let price1 = this.roundPrice(percentPn1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentPn2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentPn3 * this.totalPricePL3 / 100, 0)

				this.pricePn1 = price1
				this.pricePn2 = price2
				this.pricePn3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['so_phong_ngu'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentPn1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentPn2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentPn3 !== 0) ? 1 : 0
			}

			// if ((typeof comparisonFactor1['yeu_to_khac'] !== 'undefined') && comparisonFactor2['yeu_to_khac'] && comparisonFactor3['yeu_to_khac'] && comparisonFactor1['yeu_to_khac'].status === 1) {
			// 	let percentYtk1 = 0
			// 	let percentYtk2 = 0
			// 	let percentYtk3 = 0
			// 	this.priceYtk1 = 0
			// 	this.priceYtk2 = 0
			// 	this.priceYtk3 = 0
			// 	percentYtk1 = +comparisonFactor1['yeu_to_khac'].adjust_percent || 0
			// 	percentYtk2 = +comparisonFactor2['yeu_to_khac'].adjust_percent || 0
			// 	percentYtk3 = +comparisonFactor3['yeu_to_khac'].adjust_percent || 0
			// 	// mức điều chỉnh của yếu tố YẾU TỐ KHÁC
			// 	this.priceYtk2 = percentYtk2 * this.totalPricePL2 / 100
			// 	this.priceYtk3 = percentYtk3 * this.totalPricePL3 / 100
			// 	this.priceYtk1 = percentYtk1 * this.totalPricePL1 / 100

			// 	this.comparisonFactorChange1 += (percentYtk1 !== 0) ? 1 : 0
			// 	this.comparisonFactorChange2 += (percentYtk2 !== 0) ? 1 : 0
			// 	this.comparisonFactorChange3 += (percentYtk3 !== 0) ? 1 : 0
			// }

			// Yếu khác thêm động
			let arrayAdjustPrice = [adjustPrice1, adjustPrice2, adjustPrice3]
			let mgcd_price_other = {}
			let mgcd_price_other_abs = {}
			let arrayTem = []
			const map = new Map()
			this.price_other_comparison = []
			this.data_other_comparison.forEach(data => {
				data.other_factor_asset.forEach((item, index) => {
					if (!map.has(item.position)) {
						// set item
						map.set(item.position, true)
						// filter item which have same position
						const comparison_other = this.other_comparison.filter(data => data.position === item.position)
						comparison_other.forEach((dataOther, indexAsset) => {
							let price = this.roundPrice(dataOther.adjust_percent * arrayPriceTem[indexAsset] / 100, 0)
							arrayAdjustPrice[indexAsset] += price
							arrayTem.push({
								index: item.position,
								indication_price_asset: price,
								adjust_price_asset: arrayAdjustPrice[indexAsset]
							})
						})
						this.price_other_comparison.push(arrayTem)
						arrayTem = []
					}
				})
			})

			asset.assets_general.forEach((asset, index) => {
				const asset_comparison_data = this.other_comparison.filter(data => data.asset_general_id === asset.id)
				mgcd_price_other[index] = 0
				mgcd_price_other_abs[index] = 0
				asset_comparison_data.forEach((dataOther) => {
					if (+dataOther.adjust_percent !== 0) {
						if (asset.id === asset1.id) {
							this.comparisonFactorChange1 += 1
						} else if (asset.id === asset2.id) {
							this.comparisonFactorChange2 += 1
						} else if (asset.id === asset3.id) {
							this.comparisonFactorChange3 += 1
						}
					}
					mgcd_price_other[index] += dataOther.adjust_percent * arrayPriceTem[index] / 100
					mgcd_price_other_abs[index] += Math.abs(dataOther.adjust_percent * arrayPriceTem[index] / 100)
				})
			})

			// tổng giá trị điều chỉnh gộp
			this.tldcg1 = Math.abs(this.pricePl1) + Math.abs(this.pricePn1) + Math.abs(this.priceLch1) + Math.abs(this.priceDt1) + Math.abs(this.priceSt1) + Math.abs(this.pricePWC1) + Math.abs(mgcd_price_other_abs[0])
			this.tldcg2 = Math.abs(this.pricePl2) + Math.abs(this.pricePn2) + Math.abs(this.priceLch2) + Math.abs(this.priceDt2) + Math.abs(this.priceSt2) + Math.abs(this.pricePWC2) + Math.abs(mgcd_price_other_abs[1])
			this.tldcg3 = Math.abs(this.pricePl3) + Math.abs(this.pricePn3) + Math.abs(this.priceLch3) + Math.abs(this.priceDt3) + Math.abs(this.priceSt3) + Math.abs(this.pricePWC3) + Math.abs(mgcd_price_other_abs[2])

			// tổng giá trị điều chỉnh thuần
			this.tldc1 = this.pricePl1 + this.pricePn1 + this.priceLch1 + this.priceDt1 + this.priceSt1 + this.pricePWC1 + mgcd_price_other[0]
			this.tldc2 = this.pricePl2 + this.pricePn2 + this.priceLch2 + this.priceDt2 + this.priceSt2 + this.pricePWC2 + mgcd_price_other[1]
			this.tldc3 = this.pricePl3 + this.pricePn3 + this.priceLch3 + this.priceDt3 + this.priceSt3 + this.pricePWC3 + mgcd_price_other[2]

			// tính mức giá chỉ dẫn của TSSS
			this.mgcd1 = this.dgcc1 + this.tldc1
			this.mgcd2 = this.dgcc2 + this.tldc2
			this.mgcd3 = this.dgcc3 + this.tldc3

			if (this.mgcd1 < 0 || this.mgcd2 < 0 || this.mgcd3 < 0) {
				this.showError = true
			} else this.showError = false
			this.mgcdMin = this.mgcd1
			if (this.mgcd2 < this.mgcdMin) {
				this.mgcdMin = this.mgcd2
			}
			if (this.mgcd3 < this.mgcdMin) {
				this.mgcdMin = this.mgcd3
			}
			this.mgcdMax = this.mgcd1
			if (this.mgcd2 > this.mgcdMax) {
				this.mgcdMax = this.mgcd2
			}
			if (this.mgcd3 > this.mgcdMax) {
				this.mgcdMax = this.mgcd3
			}
			// tính mức giá trung bình của MỨC GIÁ CHỈ DẪN -- Mục 3
			this.mgtb = ((asset.assets_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.assets_general.length)) : 0
			let arrayMGTN = [this.mgcd1, this.mgcd2, this.mgcd3]
			if (this.form.appraisal_methods && this.form.appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value === 'thap-nhat') {
				this.mgtn = Math.min(...arrayMGTN)
			} else if (this.form.appraisal_methods && this.form.appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value === 'cao-nhat') {
				this.mgtn = Math.max(...arrayMGTN)
			} else if (this.form.appraisal_methods && this.form.appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value === 'trung-binh') {
				this.mgtn = ((asset.assets_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.assets_general.length)) : 0
			}

			// tỉ lệ chênh lệch mức giá trung bình trên mức giá chỉ dẫn
			this.mgcl1 = this.mgtb ? Math.round((this.mgcd1 - this.mgtb) / this.mgtb * 100, 0) : 0
			this.mgcl2 = this.mgtb ? Math.round((this.mgcd2 - this.mgtb) / this.mgtb * 100, 0) : 0
			this.mgcl3 = this.mgtb ? Math.round((this.mgcd3 - this.mgtb) / this.mgtb * 100, 0) : 0

			// biên độ điều chỉnh
			asset.assets_general.forEach(assetData => {
				// check all comparison
				let temp_comparison_factor = asset.comparison_factor.filter(item => item.type !== 'yeu_to_khac')
				if (this.other_comparison.length > 0 && temp_comparison_factor) {
					this.other_comparison.forEach(item => {
						temp_comparison_factor.push(item)
					})
				}
				// sort set min max YTSS vs YTSS khác
				const assetFilter = temp_comparison_factor.filter(item => item.asset_general_id === assetData.id && item.status === 1)
				this.area_adjusted[assetData.id] = {}
				let arrProcess = JSON.parse(JSON.stringify([...assetFilter]))
				let min = this.getArrayMin(arrProcess)
				let max = this.getArrayMax(arrProcess)
				this.area_adjusted[assetData.id]['id'] = assetData.id
				this.area_adjusted[assetData.id]['min'] = Math.abs(min)
				this.area_adjusted[assetData.id]['max'] = Math.abs(max)
			})
		}
	}
}
</script>

<style scoped lang="scss">
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
		padding: 15px 15px 15px;

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

.form-group-container {
	margin-top: 10px;
}

.color-black {
	color: #333333;
}

.btn-delete {
	cursor: pointer;
	display: flex;
	justify-content: center;
	background: #FFFFFF;
	border-radius: 5.88235px;
	padding: 0.3rem;

	@media (min-width: 1600px) {
		padding: 0.5rem;
	}

	// margin: auto;
	// width: 36px;
	// height: 36px;

	img {
		max-width: 1.5rem;
		width: 100%;
		height: auto;
	}
}

.btn {
	&-orange {
		background: #FAA831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		width: 100%;
		color: #fff;
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
	top: 30px;
	background: #FFFFFF;
	height: 2.295rem;
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
	background-color: #F6F7FB;
	border: 1px solid #E8E8E8;
	border-radius: 3px;
	padding: 16px 22px;

	.select-title {
		color: #FAA831;
		font-weight: 700;
		white-space: nowrap;
	}
}

.img_add {
	width: 100%;
	height: 100% !important;
	cursor: pointer;
}

.container_input {
	border-radius: 10px;
	border: 2px solid #FAA831;
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

.sub_header_title {
	background-color: #F6F7FB;
	border: 1px solid #E8E8E8;
	border-radius: 3px;
	padding: 0.5rem 2rem;
	position: relative;
	color: #00507C;
	font-weight: 700;
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

.sub_header_title-rows {
	padding-top: 10px;
}

.footer-title {
	font-size: 1.125rem;
	font-weight: 700;
	color: #00507C;
}

/deep/ {
	.form-group-container.disabled {
		background-color: rgba(222, 230, 238, 0.3);

		.ant-input {
			background-color: rgba(222, 230, 238, 0.3) !important;
		}
	}
	.ant-input-number:hover .ant-input-number-input{
			padding-right: 21px;
	}
	.ant-input-number-input {
		text-align: center;
	}
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

	}
}

.alertInfo {
	background-color: rgba(0, 207, 232, .12);
	color: #00CFE8 !important;
}

.content_detail_asset {
	margin-top: 1rem;
	max-height: calc(100vh - 240px) !important;
	min-height: calc(100vh - 240px) !important;

	@media (max-height: 449px){
		max-height: calc(100vh - 220px) !important;
		min-height: calc(100vh - 260px) !important;
	}
	@media (max-height: 660px){
		max-height: calc(100vh - 260px) !important;
		min-height: calc(100vh - 260px) !important;
	}

	@media (max-height: 970px) and (min-height: 660px) {
		max-height: calc(100vh - 260px) !important;
		min-height: calc(100vh - 260px) !important;
	}
}
::-webkit-scrollbar {
    width: 5px
}
::-webkit-scrollbar-thumb {
    border-radius: 10px;
    background: gray;
}
$minWidth: 170px;

.table_detail_property {
	width: 100%;
	font-weight: 500;
	color: #000000;
	text-align: center;

	thead {
		th {
			padding: 12px 0;
			font-weight: 700;
			background-color: #DEE6EE;
			;
			color: #3D4D65;
			border-right: 1px solid white;

			&:first-child {
				border-top-left-radius: 3px;
				border-left: 1px solid #CED4DA;
			}

			&:last-child {
				border-top-right-radius: 3px;
				border-right: 1px solid #CED4DA;
			}
		}
	}

	tbody {
		td {
			border: 1px solid #CED4DA;

			&:first-child {
				width: 5%;
			}

			&:nth-child(2) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(3) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(4) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(5) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(6) {
				width: 19%;
				min-width: $minWidth;
			}

			// &:last-child{
			// }
			box-sizing: border-box;

			@media (max-height: 660px) {
				padding: 8px 10px;
			}
			@media (max-height: 970px) and (min-height: 660px) {
				padding: 10px 14px;
			}
		}
	}
}

.input_center {
	text-align: center;
}

.container_round {
	padding-left: 40%;
	height: 30px;
}

.container_round_price {
	height: 30px;
}

.title_round {
	margin-right: 20px;
	padding-top: 5px
}

.td_none {
	border: none !important;
	padding: unset !important;
}

.container_btn_add {
	height: 50px;
	display: flex;
	justify-content: end;
}

.table_comparision {
	width: 100%;
	font-weight: 500;
	color: #000000;
	text-align: center;

	thead {
		th {
			padding: 12px 0;
			font-weight: 700;
			background-color: #DEE6EE;
			color: #3D4D65;
			border-right: 1px solid white;

			&:first-child {
				border-top-left-radius: 3px;
				border-left: 1px solid #CED4DA;
			}

			&:last-child {
				border-top-right-radius: 3px;
				border-right: 1px solid #CED4DA;
			}
		}
	}

	tbody {

		td {
			border: 1px solid #E5E5E5;

			&:first-child {
				width: 5%;
				min-width: 65px;
			}

			&:nth-child(2) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(3) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(4) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(5) {
				width: 19%;
				min-width: $minWidth;
			}

			&:nth-child(6) {
				width: 19%;
				min-width: $minWidth;
			}

			// &:last-child {}

			box-sizing: border-box;

			@media (max-height: 660px) {
				padding: 8px 10px;
			}
			@media (max-height: 970px) and (min-height: 660px) {
				padding: 10px 14px;
			}
		}
	}
}
.main-wrapper {
	margin-top:1rem;
	width: 100%;
	overflow-x: auto;
	box-sizing: border-box;
}
.table_result_adjust {
				width: 100%;
		font-weight: 500;
		color: #000000;
		text-align: center;
		thead{
			th{
				padding: 12px 0;
				font-weight: 700;
				background-color:  #DEE6EE;;
				color: #3D4D65;
				border-right: 1px solid white;
				&:first-child{
					border-top-left-radius: 3px;
					border-left: 1px solid #CED4DA;
				}
				&:last-child{
					border-top-right-radius: 3px;
					border-right: 1px solid #CED4DA;
				}
			}
		}
		tbody{
			// tr{
			//    &:nth-child(2) {
			//     color: #3D4D65;
			//     background-color: rgba(222, 230, 238, 0.5);
			//   }
			// }
			td{
				border: 1px solid #CED4DA;
				// &:first-child{
				//   width: 20%;
				// }
								&:nth-child(2) {
					width: 10%;
				}
				&:nth-child(3) {
					width: 13%;
				}
				&:last-child{
					width: 15%;
				}
				box-sizing: border-box;
				padding: 10px 14px;
			}
		}
	}

.other_button {
	width: 200px;
	margin-top: 15px;
}

.other_button_container {
	height: 90px;
}

.td_other_appraise {
	width: 19% !important;
	min-width: 215px
}

.td_other_asset_title {
	width: 19% !important;
	min-width: 229px;
}

.table_of_other_comparison {
	width: 100%;
	font-weight: 500;
	text-align: center;
}

.ant-table-thead {
	tr {
		th {
			text-align: center;
		}
	}
}
.ant-table-body {
  max-height: calc(100vh - 240px) !important;
  min-height: calc(100vh - 240px) !important;

  @media (max-height: 449px){
    max-height: calc(100vh - 220px) !important;
    min-height: calc(100vh - 260px) !important;
  }
  @media (max-height: 659px) and (min-height: 450px) {
    max-height: calc(100vh - 260px) !important;
    min-height: calc(100vh - 260px) !important;
  }

  @media (max-height: 970px) and (min-height: 660px) {
    max-height: calc(100vh - 260px) !important;
    min-height: calc(100vh - 260px) !important;
  }
}
.ant-table-content {
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
.document_action {
	cursor: pointer;
	background: #FFFFFF;
}
</style>
