<template>
  <div>
    <div class="card mb-0">
      <!-- <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Giá trị tài sản</h3>
          <img class="img-dropdown" :class="!showDetailContruction ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="() => { showDetailContruction = !showDetailContruction;  }">
        </div>
      </div> -->
      <div class="card-body card-info" v-show="showDetailContruction">
        <div>
          <Tabs class="tab_step_7" :theme="theme" :noTouch="true" :navAuto="true">
            <TabItem name="Bảng tổng hợp thông tin">
              <div class="content_detail_asset">
                <ValidationObserver tag="form" ref="step_7_tab1">
				<table class="table_detail_property color_content" v-if="appraises.asset_general && appraises.asset_general.length > 0">
					<thead class="ant-table-thead">
						<tr>
						<th>TT</th>
						<th>Chỉ tiêu</th>
						<th>TSTĐ</th>
						<th v-for="(asset, index) in appraises.asset_general" :key="'header' + index" >{{ 'TSSS ' + asset.id }}</th>
						</tr>
					</thead>
					<tbody class="ant-table-tbody">
						<tr>
						<td>1</td>
						<td>Loại tài sản</td>
						<td>{{appraises.asset_type ? formatSentenceCase(appraises.asset_type.description) : "-"}}</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ formatSentenceCase(asset.asset_type.description) }}</td>
						</tr>
						<tr>
						<td rowspan="2">2</td>
						<td>Loại giao dịch</td>
						<td>-</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ formatSentenceCase(asset.transaction_type.description) }}</td>
						</tr>
					<tr>
						<td>Thời điểm giao dịch</td>
						<td>-</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'transactionTime' + index">{{getTransactionTime(asset.transaction_type, asset.public_date)}}</td>
						</tr>
						<tr>
						<td>3</td>
						<td>Tọa độ</td>
						<td>
							<div>
								<div>
									{{appraises.coordinates ? `${appraises.coordinates.split(',')[0]},` : "-"}}
								</div>
								<div>
									{{appraises.coordinates ? `${appraises.coordinates.split(',')[1]}` : "-"}}
								</div>
							</div>
						</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div>
								<div>
									{{ asset.coordinates ? `${asset.coordinates.split(',')[0]},` : "-"}}
								</div>
								<div>
									{{ asset.coordinates ? `${asset.coordinates.split(',')[1]}` : "-"}}
								</div>
							</div>
						</td>
						</tr>
						<tr>
						<td>4</td>
						<td>Địa chỉ thửa đất</td>
						<td>{{appraises.full_address_appraise}}</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ asset.full_address ? asset.full_address : "-"}}</td>
						</tr>
						<tr>
						<td>5</td>
						<td>Pháp lý</td>
						<td>{{appraises.properties && appraises.properties.length > 0 ? formatSentenceCase(appraises.properties[0].legal.description) : "-"}}</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{asset.properties && asset.properties.length > 0 && asset.properties[0].legal ? formatSentenceCase(asset.properties[0].legal.description) : "-" }}</td>
						</tr>
						<tr>
						<td style="padding: unset" :rowspan="3 + appropriate_zoning_land.length + violation_zoning_land.length">6</td>
						<td>Tổng diện tích (m<sup>2</sup>)</td>
						<td>{{appraises.properties && appraises.properties.length > 0 ? formatNumber(appraises.properties[0].appraise_land_sum_area) : "-"}}</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div v-for="(property, indexProperty) in appraises.propertyChoosing.filter(i => i.asset_general_id === asset.id)" :key="'propertyArea' + indexProperty">
							{{formatNumber(property.asset_general_land_sum_area)}}
							</div>
						</td>
						</tr>
						<tr >
						<td>Đất phù hợp quy hoạch (m<sup>2</sup>)</td>
						<td>{{formatNumber(total_appraise_area)}}</td>
						<td v-for="(asset, indexAppropriateArea) in appraises.asset_general" :key="'viola' + indexAppropriateArea">
							<span :style="arrayTotalAppropriateArea[asset.id] <= 0 ? {color:'red'} : {}">{{formatNumber(arrayTotalAppropriateArea[asset.id])}}</span>
						</td>
						</tr>
						<tr v-for="(area, index) in appropriate_zoning_land" :key="'area-price' + index">
						<td>{{area.name_purpose_land}}</td>
						<td>{{formatNumber(area.appropriate_appraise_land)}}</td>
						<td v-for="(asset, index_area) in appraises.asset_general" :key="'area-land' + index_area">
							<div v-if="!checkDataPriceUBND(asset, area.asset_general_land)">
							<div v-for="(area_data, index_asset_land) in area.asset_general_land" :key="'areatype' + index_asset_land" >
								<div :style="area_data.total_area < 0 ? {color:'red'} : {}" v-if="area_data.asset_general_id === asset.id">
								{{formatNumber(area_data.total_area)}}
								</div>
							</div>
							</div>
							<div v-else>-</div>
						</td>
						</tr>
						<tr>
						<td>Đất vi phạm quy hoạch (m<sup>2</sup>)</td>
						<td>{{formatNumber(total_vio_appraise_area)}}</td>
						<td v-for="(asset, indexVio) in appraises.asset_general" :key="'viola' + indexVio">
							{{formatNumber(arrayTotalViolationArea[asset.id])}}
						</td>
						</tr>
						<tr v-for="(violation_area, indexVioArea) in violation_zoning_land" :key="'area-violation-price' + indexVioArea">
						<td>{{violation_area.name_purpose_land}}</td>
						<td>{{formatNumber(violation_area.violation_appraise_land)}}</td>
						<td v-for="(asset, index_area) in appraises.asset_general" :key="'area-violation-land' + index_area">
							<div v-if="!checkDataPriceUBND(asset, violation_area.asset_general_land)">
							<div v-for="(area_data, index_violation_land) in violation_area.asset_general_land" :key="'areaViotype' + index_violation_land" >
								<InputArea
									v-if="area_data.asset_general_id === asset.id"
									class="label-none input_center"
									:disabled="!isEditStatus"
									v-model="area_data.violation_asset_area"
									vid="violation_asset_area"
									label="area-violation"
									:text_center="true"
									:max="999999999"
									:min="-1"
									:decimal="2"
									@change="changeViolationLand($event, area_data, index_area)"
								/>
							</div>
							</div>
							<div v-else>-</div>
						</td>
						</tr>
						<!-- Price UBND -->
						<tr v-for="(data, index) in data_land_price_demo" :key="'body-price' + index" >
						<td v-if="index === 0" style="padding: unset" :rowspan="data_land_price_demo.length">7</td>
						<td>{{`Đơn giá ${data.purpose_land} theo UBND` }}</td>
						<td>
							<div v-if="data.price_appraise">
							{{formatNumber(data.price_appraise) + ' đ'}}
							</div>
							<div v-else>-</div>
						</td>
						<td v-for="(asset, index_price) in appraises.asset_general" :key="'assetType' + index_price">
							<div v-if="!checkDataPriceUBND(asset, data.price_asset)">
							<div v-for="(price_data, index) in data.price_asset.filter(i => i.asset_general_id === asset.id)" :key="'pricetype' + index">
								<InputCurrency
									:disabled="!isEditStatus"
									:class="{'label-none input_center ': true, 'input_number_error': price_data.update_value < 0 }"
									v-model="price_data.update_value"
									vid="price_asset"
									:text_center="true"
									:max="999999999999999"
									@change="changePriceLand($event, price_data, index_price)"
								/>
							</div>
							</div>
							<div v-else>
							<div>-</div>
							</div>
						</td>
						</tr>
						<tr>
						<td>8</td>
						<td>Chiều rộng</td>
						<td>
						<!-- <div :key="'widthStreet'">{{`${appraises.comparison_factor[0].comparison_factor.find(i => i.type === 'chieu_rong_mat_tien').appraise_title}m`}}</div> -->
						<div :key="'widthStreet'">{{formatNumber(`${getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'chieu_rong_mat_tien', false)}`)}}m</div>
						</td>
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetWidthStreet' + index">
							<!-- <span :key="'chieu_rong_mat_tien' + asset.id">{{`${asset.comparison_factor.find(i => i.type === 'chieu_rong_mat_tien').asset_title}m`}}</span> -->
							<span :key="'chieu_rong_mat_tien' + asset.id">{{formatNumber(`${getComparisonTitleByType(asset.comparison_factor, 'chieu_rong_mat_tien', true)}`)}}m</span>
						</td>
						</tr>
						<tr>
						<td>9</td>
						<td>Chiều dài</td>
						<td>
							<!-- <div v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'widthLongLand' + index">{{`${comparison_factor.appraise_title}m`}}</div> -->
							<div :key="'widthLongLand'">{{formatNumber(`${getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'chieu_sau_khu_dat', false)}`)}}m</div>
						</td>
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetLongLand' + index">
							<!-- <span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' + index">{{`${comparison_factor.asset_title}m`}}</span> -->
							<span :key="'chieu_sau_khu_dat' + asset.id">{{formatNumber(`${getComparisonTitleByType(asset.comparison_factor, 'chieu_sau_khu_dat', true)}`)}}m</span>
						</td>
						</tr>
						<tr>
						<td>10</td>
						<td>Hình dáng</td>
						<td>
							<div v-if="appraises.properties && appraises.properties.length > 0 && appraises.properties[0].land_shape">{{formatSentenceCase(appraises.properties[0].land_shape.description)}}</div>
							<div v-else>-</div>
						</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div v-if="asset.properties && asset.properties.length > 0 && asset.properties[0].land_shape">{{formatSentenceCase(asset.properties[0].land_shape.description)}}</div>
							<div v-else>-</div>
						</td>
						</tr>
						<tr>
						<td rowspan="4">11</td>
						<td>Kết cấu xây dựng</td>
						<td>
							<div v-if="appraises.tangible_assets && appraises.tangible_assets.length > 0">{{formatSentenceCase(appraises.tangible_assets[0].building_type.description)}}</div>
							<div v-else>-</div>
						</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div v-if="asset.tangible_assets && asset.tangible_assets.length > 0">{{ formatSentenceCase(asset.tangible_assets[0].building_type.description) }}</div>
							<div v-else>-</div>
						</td>
						</tr>
						<tr>
						<td>DTSXD</td>
						<td>
							<div v-if="appraises.tangible_assets && appraises.tangible_assets.length > 0">{{ formatNumber(appraises.tangible_assets[0].total_construction_base) }}m<sup>2</sup></div>
							<div v-else>-</div>
						</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div v-if="asset.tangible_assets && asset.tangible_assets.length > 0">{{ formatNumber(asset.tangible_assets[0].total_construction_base) }}m<sup>2</sup></div>
							<div v-else>-</div>
						</td>
						</tr>
						<tr>
						<td>Tỷ lệ CLCL</td>
						<td>
							<div v-if="appraises.tangible_assets && appraises.tangible_assets.length > 0">{{formatNumber(appraises.tangible_assets[0].remaining_quality)}}%</div>
							<div v-else>-</div>
						</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div v-if="asset.tangible_assets && asset.tangible_assets.length > 0">{{ formatNumber(asset.tangible_assets[0].remaining_quality)}}%</div>
							<div v-else>-</div>
						</td>
						</tr>
						<tr>
						<td>Đơn giá xây dựng mới</td>
						<td>-</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetConstructionAmount' + index">
							{{asset.tangible_assets && asset.tangible_assets.length > 0 ? (asset.tangible_assets[0].unit_price_m2 ? formatNumber(asset.tangible_assets[0].unit_price_m2) : 0) + ' đ' : '-' }}
							</td>
						</tr>
						<tr>
						<td>12</td>
						<td>Giá trị xây dựng còn lại</td>
						<td>-</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
							<div v-if="asset.tangible_assets && asset.tangible_assets.length > 0 && asset.tangible_assets[0]">{{remainingBuildingPrice(asset.tangible_assets[0])}} </div>
							<div v-else>-</div>
						</td>
						</tr>
						<tr>
						<td>13</td>
						<td>Vị trí</td>
						<td>{{ (typeof appraises.properties !== 'undefined') && appraises.properties.length > 0 ? appraises.properties[0].description : '' }}</td>
						<td v-for="(asset, indexAsset) in appraises.asset_general" :key="'assetProperty' + indexAsset">
							<span v-for="(property, index) in asset.properties.filter(i => i.id === appraises.appraise_has_assets[indexAsset].asset_property_detail_id)" :key="'property' +index">{{property.description ? property.description : '-'}}</span>
						</td>
						</tr>
						<tr>
						<td>14</td>
						<td>Kết cấu giao thông</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'appraisalTraffic' + index">{{formatSentenceCase(comparison_factor.appraise_title)}}</td> -->
						<td :key="'appraisalTraffic'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'ket_cau_duong', false))}}</td>
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetTraffic' + index">
							<!-- <span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ket_cau_duong' +index">{{formatSentenceCase(comparison_factor.asset_title)}}</span> -->
							<span :key="'ket_cau_duong' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'ket_cau_duong', true))}}</span>
						</td>
						</tr>
						<tr>
						<td>15</td>
						<td>Độ rộng đường</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'appraisalStreet' + index">{{comparison_factor.appraise_title}}m</td> -->
						<td :key="'appraisalStreet'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'do_rong_duong', false))}}</td>
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index">
							<!-- <span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +index">{{comparison_factor.asset_title}}m</span> -->
							<span :key="'do_rong_duong' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'do_rong_duong', true))}}</span>
						</td>
						</tr>
						<tr>
						<td>16</td>
						<td>Cơ sở hạ tầng</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'appraisalBasis' + index">{{formatSentenceCase(comparison_factor.appraise_title)}}</td> -->
						<td :key="'appraisalBasis'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'dieu_kien_ha_tang', false))}}</td>
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetBasis' + index">
							<!-- <span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +index">{{formatSentenceCase(comparison_factor.asset_title)}}</span> -->
							<span :key="'dieu_kien_ha_tang' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'dieu_kien_ha_tang', true))}}</span>
						</td>
						</tr>
						<tr>
						<td>17</td>
						<td>Lợi thế kinh doanh</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'appraisalBussiness' + index">{{formatSentenceCase(comparison_factor.appraise_title)}}</td> -->
						<td :key="'appraisalBussiness'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'kinh_doanh', false))}}</td>
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetBussiness' + index">
							<!-- <span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +index">{{formatSentenceCase(comparison_factor.asset_title)}}</span> -->
							<span :key="'kinh_doanh' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'kinh_doanh', true))}}</span>
						</td>
						</tr>
						<tr>
						<td>18</td>
						<td>An ninh, môi trường sống</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'appraisalSecurity' + index">{{formatSentenceCase(comparison_factor.appraise_title)}}</td> -->
						<td :key="'appraisalSecurity'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'an_ninh_moi_truong_song', false))}}</td>
						<!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +index">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td> -->
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span :key="'an_ninh_moi_truong_song' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'an_ninh_moi_truong_song', true))}}</span></td>
						</tr>
						<tr>
						<td>19</td>
						<td>Phong thủy</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'appraisalSecurity' + index">{{formatSentenceCase(comparison_factor.appraise_title)}}</td> -->
						<td :key="'appraisalFengshui'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'phong_thuy', false))}}</td>
						<!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +index">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td> -->
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'appraisalFengshui' + index"><span :key="'phong_thuy' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'phong_thuy', true))}}</span></td>
						</tr>
						<tr>
						<td>20</td>
						<td>Điều kiện thanh toán</td>
						<!-- <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'appraisalPay' + index">{{formatSentenceCase(comparison_factor.appraise_title)}}</td> -->
						<td :key="'appraisalPay'">{{formatSentenceCase(getComparisonTitleByType(appraises.comparison_factor[0].comparison_factor, 'dieu_kien_thanh_toan', false))}}</td>
						<!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetPay' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +index">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td> -->
						<td v-for="(asset, index) in appraises.comparison_factor" :key="'appraisalPay' + index"><span :key="'dieu_kien_thanh_toan' + asset.id">{{formatSentenceCase(getComparisonTitleByType(asset.comparison_factor, 'dieu_kien_thanh_toan', true))}}</span></td>
						</tr>
						<tr>
						<td>21</td>
						<td>Giá rao bán</td>
						<td>-</td>
						<td v-for="(asset, index) in appraises.asset_general" :key="'totalAmount' + index">{{ formatNumber(asset.total_amount) }} đ</td>
						</tr>
						<tr>
						<td>22</td>
						<td>Tỷ lệ giá rao bán</td>
						<td>-</td>
						<td v-for="(asset, index) in form.appraise_adapter" :key="'adjustPercent' + index">
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
						<td>23</td>
						<td>Tổng giá trị tài sản ước tính</td>
						<td>-</td>
						<td>{{formatNumber(totalPriceEstimate1)}} đ</td>
						<td>{{formatNumber(totalPriceEstimate2)}} đ</td>
						<td>{{formatNumber(totalPriceEstimate3)}} đ</td>
						</tr>
						<tr>
						<td>24</td>
						<td>Giá trị phần diện tích vi phạm quy hoạch</td>
						<td>-</td>
						<td v-for="(asset, indexVioPrice) in form.appraise_adapter" :key="'changeViolationPrice' + indexVioPrice">
							<InputCurrency
								:disabled="!isEditStatus"
								:key="key_render_1"
								class="label-none input_center"
								v-model="asset.change_violate_price"
								vid="change_violate_price"
								:text_center="true"
								defaultValue="0"
								@change="changeViolationPrice($event, indexVioPrice)"
								@error="errorViolationPrice($event, indexVioPrice)"
							/>
						</td>
						<!-- <td>{{totalPriceViolationArea1 ? formatNumber(totalPriceViolationArea1) : 0}} đ</td>
						<td>{{totalPriceViolationArea2 ? formatNumber(totalPriceViolationArea2) : 0}} đ</td>
						<td>{{totalPriceViolationArea3 ? formatNumber(totalPriceViolationArea3) : 0}} đ</td> -->
						</tr>
						<tr>
						<td>25</td>
						<td>Chi phí chuyển mục đích sử dụng</td>
						<td>-</td>
						<td v-for="(asset, index) in form.appraise_adapter" :key="'changePurposePrice' + index">
							<InputNumberNegative
								:disabled="!isEditStatus"
								:key="key_render_1"
								class="label-none input_center input_highlight"
								:required="true"
								v-model="asset.change_purpose_price"
								vid="change_purpose_price"
								:max="999999999999999"
								:text_center="true"
								@change="changePurposePrice($event, index)"
							/>
						</td>
						</tr>
						<tr>
						<td>26</td>
						<td>Giá trị QSDĐ {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}} ước tính</td>
						<td>-</td>
						<td>{{formatNumber(totalPrice1)}} đ</td>
						<td>{{formatNumber(totalPrice2)}} đ</td>
						<td>{{formatNumber(totalPrice3)}} đ</td>
						</tr>
						<tr>
						<td>27</td>
						<td>Đ/giá {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}} B.Quân.</td>
						<td>-</td>
						<td>{{formatNumber(parseFloat(dgd1).toFixed(0))}} đ</td>
						<td>{{formatNumber(parseFloat(dgd2).toFixed(0))}} đ</td>
						<td>{{formatNumber(parseFloat(dgd3).toFixed(0))}} đ</td>
						</tr>
					</tbody>
				</table>

				<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
				<div class="d-md-flex d-block">
					<button  @click="onCancel" class="btn btn-white text-nowrap" >
						<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Thoát
					</button>
					<!-- <button v-if="(edit || add) && (status === 1 || status === 2) && checkRole" :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="handlePrint">
						<img src="@/assets/icons/ic_printer_white.svg" style="margin-right: 12px" alt="print"/>In báo cáo sơ bộ
					</button> -->
					<button v-if="isEditStatus" :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="handleSaveTab1">
						<img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
					</button>
				</div>
				</div>
                </ValidationObserver>
              </div>
            </TabItem>
            <TabItem class="item_2" name="Bảng điều chỉnh QSDĐ">
              <div class="content_detail_asset">
                <table class="table_comparision color_content" v-if="appraises.comparison_factor && appraises.comparison_factor.length > 0">
                    <thead class="ant-table-thead">
                        <tr>
                        <th>TT</th>
                        <th>Yếu tố so sánh</th>
                        <th>TSTĐ</th>
                        <th v-for="(asset, index) in appraises.comparison_factor" :key="'headerElement' + index" >{{ 'TSSS ' + asset.id }}</th>
                        </tr>
                    </thead>
                    <tbody class="ant-table-tbody">
                        <tr>
                        <td>1</td>
                        <td>Đơn giá quyền sử dụng đất (đồng/m<sup>2</sup>)</td>
                        <td>Chưa biết</td>
                        <td>{{formatNumber(parseFloat(dgd1).toFixed(0))}} đ</td>
                        <td>{{formatNumber(parseFloat(dgd2).toFixed(0))}} đ</td>
                        <td>{{formatNumber(parseFloat(dgd3).toFixed(0))}} đ</td>
                        </tr>
<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'appraisalLegalRow' + index">
                        <td rowspan="4">A</td>
                        <td><strong>Pháp lý</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)"  :key="'appraisalLegal' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetInfo' + index"><span  v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'phap_ly' + indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'changeLegalRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputLegal' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'phap_ly' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'LegalRow1' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(pricePl1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(pricePl2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(pricePl3).toFixed(0))}}</td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1)" :key="'LegalRow2' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(parseFloat(totalPricePL1).toFixed(0))}}</strong></td>
                        <td><strong>{{formatNumber(parseFloat(totalPricePL2).toFixed(0))}}</strong></td>
                        <td><strong>{{formatNumber(parseFloat(totalPricePL3).toFixed(0))}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('quy_mo')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Quy mô</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo1' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}m<sup>2</sup></td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetQuymo' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo2' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}m<sup>2</sup></span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo3' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputQuymo' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo4' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo5' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceQm1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceQm2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceQm3).toFixed(0))}}</td>
                        </tr>
												<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1)" :key="'quymo6' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['quy_mo'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['quy_mo'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['quy_mo'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'appraisalWidthRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('chieu_rong_mat_tien')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Chiều rộng mặt tiền</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'appraisalWidth' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}m</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'chieu_rong_mat_tien' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}m</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'WidthRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputWidth' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'chieu_rong_mat_tien' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'WidthChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceCrmt1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceCrmt2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceCrmt3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1)" :key="'WidthChangeRow2' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['chieu_rong_mat_tien'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['chieu_rong_mat_tien'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['chieu_rong_mat_tien'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'appraisalDepthRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('chieu_sau_khu_dat')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Chiều sâu khu đất</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'appraisalDepth' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}m</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'chieu_sau_khu_dat' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}m</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'DepthRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputDepth' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'chieu_sau_khu_dat' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'DepthChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceCskd1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceCskd2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceCskd3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1)" :key="'DepthChangeRow2' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['chieu_sau_khu_dat'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['chieu_sau_khu_dat'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['chieu_sau_khu_dat'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'appraisalLandRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('hinh_dang_dat')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Hình dáng</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'appraisalLand' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'hinh_dang_dat' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'LandRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputLand' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'hinh_dang_dat' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'LandChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceHdd1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceHdd2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceHdd3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1)" :key="'LandChangeRow2' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['hinh_dang_dat'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['hinh_dang_dat'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['hinh_dang_dat'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
						<!-- Kết cấu đường -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('ket_cau_duong')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Kết cấu đường</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong1' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetKetcauduong' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong2' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong3' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputKetcauduong' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong4' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong5' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceKcd1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceKcd2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceKcd3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1)" :key="'ketcauduong6' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['ket_cau_duong'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['ket_cau_duong'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['ket_cau_duong'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'appraisalStreetRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('do_rong_duong')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Độ rộng đường</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'appraisalStreet' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}m</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'do_rong_duong' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}m</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'StreetRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputStreet' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'do_rong_duong' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'StreetChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceDrd1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceDrd2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceDrd3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1)" :key="'StreetChangeRow2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['do_rong_duong'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['do_rong_duong'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['do_rong_duong'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'appraisalConditionsRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('dieu_kien_ha_tang')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Điều kiện hạ tầng</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'appraisalConditions' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'dieu_kien_ha_tang' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'ConditionsRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputConditions' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'dieu_kien_ha_tang' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'ConditionsChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceDkht1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceDkht1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceDkht1).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1)" :key="'ConditionsChangeRow2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['dieu_kien_ha_tang'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['dieu_kien_ha_tang'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['dieu_kien_ha_tang'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'appraisalBusinessRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('kinh_doanh')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Kinh doanh</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'appraisalBusiness' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'kinh_doanh' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'BusinessRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputBusiness' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'kinh_doanh' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'BusinessChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceKd1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceKd2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceKd3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1)" :key="'kinh_doanh2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['kinh_doanh'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['kinh_doanh'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['kinh_doanh'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'appraisalSecurityRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('an_ninh_moi_truong_song')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>An ninh, môi trường sống</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'appraisalSecurity' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'an_ninh_moi_truong_song' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'SecurityRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputSecurity' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'an_ninh_moi_truong_song' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'SecurityChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceAnmts1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceAnmts2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceAnmts3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1)" :key="'SecurityChangeRow2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['an_ninh_moi_truong_song'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['an_ninh_moi_truong_song'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['an_ninh_moi_truong_song'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'appraisalFengShuiRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('phong_thuy')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Phong thủy</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'appraisalFengShui' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'phong_thuy' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'FengShuiRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputFengShui' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'phong_thuy' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'TrafficChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                            <td>{{formatNumber(parseFloat(pricePt1).toFixed(0))}}</td>
                            <td>{{formatNumber(parseFloat(pricePt2).toFixed(0))}}</td>
                            <td>{{formatNumber(parseFloat(pricePt3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1)" :key="'TrafficChangeRow2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['phong_thuy'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['phong_thuy'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['phong_thuy'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'appraisalTrafficRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('giao_thong')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                            </td>
                        <td><strong>Giao thông</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'appraisalTraffic' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'giao_thong' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'TrafficRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputTraffic' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'giao_thong' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'TrafficChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                            <td>{{formatNumber(parseFloat(priceGt1).toFixed(0))}}</td>
                            <td>{{formatNumber(parseFloat(priceGt2).toFixed(0))}}</td>
                            <td>{{formatNumber(parseFloat(priceGt3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1)" :key="'TrafficChangeRow2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['giao_thong'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['giao_thong'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['giao_thong'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach' + index">
                        <td rowspan="3">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('quy_hoach')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Quy hoạch/Hiện trạng</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach1' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetQuyhoach' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach2' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach3' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputQuyhoach' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach4' + index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach5' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceQh1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceQh2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceQh3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1)" :key="'quyhoach6' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['quy_hoach'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['quy_hoach'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['quy_hoach'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'appraisalPayRow' + index">
                        <td rowspan="4">
                            <div class="btn-delete" v-if="isEditStatus" type="button" @click="dialogDeleteComparisionDefault('dieu_kien_thanh_toan')"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                        </td>
                        <td><strong>Điều kiện thanh toán</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'appraisalPay' + index">{{formatSentenceCase(comparison_factor_appraise.appraise_title)}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'dieu_kien_thanh_toan' +indexItem">{{formatSentenceCase(comparison_factor.asset_title)}}</span></td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'PayRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'inputPay' + indexAsset">
                            <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexAsset].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'dieu_kien_thanh_toan' +index">
                            <InputPercentNegative
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="comparison_factor.adjust_percent"
                                vid="number_legal"
                                :text_center="true"
                                @change="changeLegalRate($event, indexAsset, comparison_factor.type)"
                            />
                            </div>
                        </td>
                        </tr>

                        <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'PayChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{formatNumber(parseFloat(priceDktt1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceDktt2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(priceDktt3).toFixed(0))}}</td>
                        </tr>
						<tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor.filter(comparison_factor_appraise => comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1)" :key="'PayChangeRow2' + index">
						<td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{formatNumber(adjustPriceData['dieu_kien_thanh_toan'][0])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['dieu_kien_thanh_toan'][1])}}</strong></td>
                        <td><strong>{{formatNumber(adjustPriceData['dieu_kien_thanh_toan'][2])}}</strong></td>
                        </tr>
<!-- Adjust Comparison END -->

<!-- Adjust Comparison BEGIN -->
                        <tr v-if="showOtherFactor && data_other_comparison && data_other_comparison.length > 0"
                            v-for="(data_appraise, index) in data_other_comparison || []"
                            :key="'yeu_to_khac' + index">
                            <td style="padding: unset" colspan="6">
                            <table class="table_of_other_comparison color_content">
                            <tr style="width: 100% !important">
                                <td rowspan="4">
                                <div v-if="isEditStatus" class="btn-delete" type="button" @click="handleDialogDeleteComparision(index, data_appraise.other_factor_asset)"><img src="@/assets/icons/ic_delete_2.svg" alt="save"></div>
                                </td>
                                <td class="td_other_appraise">
                                <InputText
                                    class="inputLabel inputLabelCompare"
																		:disabled="!isEditStatus"
                                    v-model="data_appraise.name"
                                    vid="description"
                                    styleInput="text-align:center;font-weight:bold"
                                @change="handleChangeNameFactor($event, data_appraise.other_factor_asset)"
                                />
                                </td>
                                <td class="td_other_asset_title">
                                <InputText
                                    v-model="data_appraise.appraise_title"
																		:disabled="!isEditStatus"
                                    vid="description"
                                    styleInput="text-align:center"
                                @change="handleChangeTitleAppraise($event, data_appraise.other_factor_asset)"
                                />
                                </td>
                                <td class="td_other_asset_title" v-for="(data_asset, indexItem) in data_appraise.other_factor_asset" :key="'yeutokhac2' +indexItem">
                                <div >
                                    <InputText
																			v-model="data_asset.asset_title"
																			:disabled="!isEditStatus"
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
                                    <InputPercentNegative
																			:disabled="!isEditStatus"
																			class="label-none input_center"
																			v-model="rate_asset.adjust_percent"
																			vid="number_legal"
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
<!-- Adjust Comparison END -->
                        <tr v-if="isEditStatus" class="other_button_container" >
                        <td class="td_none"></td>
                        <td class="td_none" colspan="5">
                            <div class="container_btn_add">
                            <button class="btn text-warning btn-ghost other_button" type="button" @click="handleAddOtherFactor" >+ Thêm yếu tố khác</button>
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
                        <td v-for="(asset, index_asset) in appraises.asset_general" :key="'asset_area_adjusted' + index_asset" >
                            <div v-for="(percent, index) in area_adjusted" :key="'percent_adjust' + index" v-if="percent.id === asset.id">
                                {{`${checkMin(area_adjusted[asset.id])} - ${checkMax(area_adjusted[asset.id])}`}}
                            </div>
                        </td>
                        </tr>
                        <tr>
                        <td>8</td>
                        <td colspan="2">Tổng giá trị điều chỉnh thuần (đ/m<sup>2</sup>)</td>
                        <td>{{formatNumber(parseFloat(tldc1).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(tldc2).toFixed(0))}}</td>
                        <td>{{formatNumber(parseFloat(tldc3).toFixed(0))}}</td>
                        </tr>
                        <tr>
                        <td>9</td>
                        <td colspan="2">
                            <div class="d-flex col-12 col-lg-12">
                            <div class="col-4 col-lg-4"></div>
                            <div class="d-flex col-8 col-lg-8">
                                <strong style="margin-top: 2px; margin-right: 30px">Thống nhất mức giá chỉ dẫn</strong>
                                <InputSwitchLayerCuting
																	:disabled="!isEditStatus"
																	v-if="this.env != 'trial'"
																	v-model="layer_cutting_procedure"
																	vid="layer_cutting_procedure"
																	@input="changeLayerCuttingProcedure"
                                />

                            </div>
                            </div>
                        </td>
                        <td colspan="3" align="center"><strong>{{formatNumber(getMainPrice)}}</strong></td>
                        </tr>
                        <tr v-if="mainPrice.islayerCuttingPirce">
                        <td>9.1</td>
                        <td colspan="2"> Đơn giá sau cắt lớp</td>
                        <td colspan="3" align="center">
                            <div style="padding: 0 10rem">
															<InputCurrency
																:disabled="!isEditStatus"
                                class="label-none input_center"
                                v-model="mainPrice.layerCuttingPirce"
                                vid="layer_cutting_procedure_price"
                                label="Đơn giá cắt lớp"
                                :max="999999999999999"
                                :min="0"
																:required="true"
                                @change="changeLayerCuttingPrice($event)"
																@error="changeLayerCuttingPriceError($event)"
															/>

                            </div>
                        </td>
                        </tr>
                        <tr>
                        <td>10</td>
                        <td colspan="2">
                            <div class="d-flex col-12 col-lg-12 align-items-center container_round">
                                <a class="pr-3">Làm tròn</a>
                                <div style="width: 70px">
                                    <InputNumberFormat
																			:disabledInput="!isEditStatus"
																			v-model="mainPrice.round"
																			vid="round_total"
																			:max="10"
																			:min="-10"
																			@change="changeRoundTotal($event)"
                                    />
                                </div>
                            </div>
                        </td>
                        <td colspan="3" align="center">{{formatNumber(calMainRoundPrice)}} đ</td>
                        </tr>

                        <tr v-for="(purpose, index) in purposePrice" :key="'purposePrice' + index">
							<td>{{purposePrice.length > 1 ? 11 + '.' + (index + 1) : 11}}</td>
							<td colspan="2">
								<div>
								Đơn giá đất {{purpose.acronym}} thị trường
								</div>
								<div>({{type_method}})</div>
							</td>
							<td>
								<div>
								<InputCurrency
									:key="purpose.price"
									:class="{'label-none input_center ': true}"
									v-model="purpose.price"
									:vid="purpose.acronym + index"
									:text_center="true"
									:max="999999999999999"
									:min="0"
									:disabled="checkProcedure || !isEditStatus"
									:required="true"
									@change="changeDynamicPurposePrice($event, index)"
									@error="errorDynamicPurposePrice($event, index)"
								/>
								</div>
							</td>
							<td>
								<div class="d-flex col-12 col-lg-12 align-items-center justify-content-center">
								<a class="pr-3">Làm tròn</a>
								<div style="width: 70px">
									<InputNumberFormat
										:disabledInput="!isEditStatus"
										v-model="purpose.round"
										:vid="purpose.acronym + index"
										:max="10"
										:min="-10"
										@change="changeDynamicPurposeRound($event, index)"
									/>
								</div>
								</div>
							</td>
							<td>{{`${formatNumber(calPurposePriceByIndex(index))} đ`}}</td>
						</tr>
						<tr v-for="(violate, index) in violatePrice" :key="'violatePrice' + index">
							<td>{{purposePrice.length > 0 ? (violatePrice.length > 1 ? 12 + '.' + (index + 1) : 12) : (violatePrice.length > 1 ? 11 + '.' + (index + 1) : 11)}}</td>
							<td colspan="2">
								<div>
								Đơn giá đất {{violate.acronym}} vi phạm quy hoạch
								</div>
								<div>({{type_violation_method}})</div>
							</td>
							<td>{{formatNumber(parseInt(violate.price))}}</td>
							<td>
								<div class="d-flex col-12 col-lg-12 align-items-center justify-content-center">
								<a class="pr-3">Làm tròn</a>
								<div style="width: 70px">
									<InputNumberFormat
										:disabledInput="!isEditStatus"
										v-model="violate.round"
										:vid="violate.acronym + index"
										:max="10"
										:min="-10"
										@change="changeDynamicViolateRound($event, index)"
									/>
								</div>
								</div>
							</td>
							<td>{{`${formatNumber(roundPrice(violate.price, violate.round))} đ`}}</td>
                        </tr>
                    </tbody>
                </table>
                 <!--------------------------- KẾT QUẢ THẨM ĐỊNH GIÁ ----------------------------------->
                 <div class="col-12 mt-4">
                    <div class="d-flex align-items-center sub_header_title">
                        <span class="label">Bảng tổng hợp</span>
                    </div>
                    <div class="main-wrapper mb-2">
                        <div class="responsive-table">
							<table class="table_result_adjust">
								<thead>
									<tr>
									<th>Tên tài sản</th>
									<th>MĐSD</th>
									<th>Diện tích (m<sup>2</sup>)</th>
									<th>Đơn giá (đ)</th>
									<th>Thành tiền (đ)</th>
									</tr>
								</thead>
								<tbody >
									<!---------------------------------------------- phần Diện tích PHÙ HỢP quy hoạch--------------------------------------------->
									<tr v-if="mainPrice">
									<td style="width:28%" :rowspan="purposePrice.length + 1">Phần diện tích đất phù hợp quy hoạch</td>
									<td>{{mainPrice.acronym}}</td>
									<td class="text-right">{{formatNumber(mainArea)}}</td>
									<td class="text-right">{{formatNumber(calMainRoundPrice)}}</td>
									<td class="text-right">{{formatNumber(calMainTotalPrice)}}</td>
									</tr>
									<tr v-for="(purpose, index) in purposePrice" :key="'purposePrice' + index">
									<td>{{purpose.acronym}}</td>
									<td class="text-right">{{formatNumber(purpose.area.toFixed(2))}}</td>
									<td class="text-right">{{formatNumber(roundPrice(purpose.price, purpose.round)) }}</td>
									<td class="text-right">{{formatNumber(roundPrice(roundPrice(purpose.price, purpose.round) * purpose.area), 0)}}</td>
									</tr>
									<!----------------------------------------------- phần Diện tích VI PHẠM quy hoạch ----------------------------------------------->
								<tr v-for="(violate, index) in violatePrice" :key="'violatePrice' + index">
									<td style="width:28%" v-if="index === 0" :rowspan="violatePrice.length">Phần diện tích đất vi phạm quy hoạch</td>
									<td>{{violate.acronym}}</td>
									<td class="text-right">{{formatNumber(violate.area.toFixed(2))}}</td>
									<td class="text-right">{{formatNumber(roundPrice(violate.price, violate.round))}}</td>
									<td class="text-right">{{formatNumber(roundPrice(roundPrice(violate.price, violate.round) * violate.area), 0)}}</td>
									</tr>
									<td colspan="2"><strong>Tổng cộng</strong></td>
									<td class="text-right">
										<strong>
											{{formatNumber(getTotalArea)}}
										</strong>
									</td>
									<td></td>
									<td class="text-right">
										<strong>
											{{formatNumber(calTotalLandPrice)}}
										</strong>
									</td>
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
                    <button v-if="isEditStatus" :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="handleSaveTab2Ver2">
                      <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
                    </button>
                  </div>
                </div>
              </div>

          </TabItem>
            <TabItem class="item_3" name="Bảng điều chỉnh CTXD">
              <ModalStep7TabContruction
                :data="form"
                :idData="idData"
                :edit="edit"
                :checkRole="checkRole"
                :status="status"
                :constructions="constructions"
                :construction_company_ids="construction_company_ids"
								:editAsset="isEditStatus"
								:constructionRemainQuality="constructionRemainQuality"
								:constructionPriceType="constructionPriceType"
								:key="render_construction"
                @updateAssetPrice="updateAssetPrice"
              />

            </TabItem>
            <TabItem class="item_4" name="Tài sản khác">
              <ModalStep7TabOtherProperty
                :data="form"
                :edit="edit"
                :checkRole="checkRole"
                :idData="idData"
                :status="status"
								:editAsset="isEditStatus"
                @updateTotalOtherAsset="updateTotalOtherAsset"  />
              </TabItem>
            <TabItem class="item_5" name="Tổng hợp kết quả">
              <ModalStep7Summarize
							:key="key_render_5"
							:data="form"
							:idData="idData"
							:edit="edit"
							:checkRole="checkRole"
							:status="status"
							:editAsset="isEditStatus"
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
	<ModalPrintEstimateAssets
		v-if="openPrint"
		@cancel="openPrint = false"
		:data="reportData"
    />
  </div>
</template>

<script>
import InputNumberFormat from '@/components/Form/InputNumber'
import { AlertCircleIcon } from 'vue-feather-icons'
import InputPercent from '@/components/Form/InputPercent'
import InputPercentNegative from '@/components/Form/InputPercentNegative'
import InputNumberNew from '@/components/Form/InputNumberNew'
import InputCurrency from '@/components/Form/InputCurrency'
import InputCategoryBoolean from '@/components/Form/InputCategoryBoolean'
import InputArea from '@/components/Form/InputArea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputNumberNegative from '@/components/Form/InputNumberNegative'
import InputSwitch from '@/components/Form/InputSwitch'
import InputLengthArea from '@/components/Form/InputLengthArea'
import { Tabs, TabItem } from 'vue-material-tabs'
import InputSwitchLayerCuting from '@/components/Form/InputSwitchLayerCuting'
import ModalDelete from '@/components/Modal/ModalDelete'
import CertificateAsset from '@/models/CertificateAsset'
import ModalStep7TabContruction from './modals/ModalStep7TabContruction'
import ModalStep7TabOtherProperty from './modals/ModalStep7TabOtherProperty'
import ModalStep7Summarize from './modals/ModalStep7Summarize'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import { BAlert } from 'bootstrap-vue'
import Vue from 'vue'
import Icon from 'buefy'
import ModalPrintEstimateAssets from '@/components/Modal/ModalPrintEstimateAsset'
import { toLower } from 'lodash-es'

Vue.use(Icon)

export default {
	name: 'Step7',
	props: ['data', 'dataStep1', 'dataStep2', 'dataStep3', 'dataStep4', 'dataStep5', 'dataStep6', 'full_address_street', 'landShapes',
		'idData', 'constructions', 'construction_company_ids', 'edit', 'add', 'status', 'checkRole', 'jsonConfig', 'isEditStatus', 'constructionRemainQuality', 'constructionPriceType'],
	components: {
		Tabs,
		TabItem,
		InputCategory,
		ModalDelete,
		InputText,
		InputNumberNegative,
		InputSwitch,
		InputLengthArea,
		InputArea,
		InputDatePicker,
		InputCategoryBoolean,
		InputCurrency,
		InputNumberNew,
		InputPercent,
		InputPercentNegative,
		BAlert,
		AlertCircleIcon,
		InputSwitchLayerCuting,
		ModalStep7TabContruction,
		ModalStep7TabOtherProperty,
		ModalStep7Summarize,
		InputNumberFormat,
		ModalNotificationAppraisal,
		ModalPrintEstimateAssets
	},

	data () {
		return {
			theme: {
				navItem: '#3D4D65',
				navActiveItem: '#007EC6',
				slider: '#007EC6',
				arrow: '#3D4D65'
			},
			env: '',
			isSubmit: false,
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			key_render_5: 900000,
			key_render_1: 7000000,
			key_render_around: 600023,
			showDetailContruction: true,
			appraises: {},
			showAreaLand: true,
			showPriceCommitee: true,
			showDetailAppraise: true,
			showDetailComparison: true,
			showResultAppraise: true,
			type_method: '',
			type_violation_method: '',
			typeComparision: '',
			openMdodalDeleteDefault: false,
			checkProcedure: false,
			remaining_commerce_price: '',
			type_is_not_facility: '',
			indexDeleteComparision: null,
			dataDeleteComparision: {},
			openModalDelete: false,
			indicativePrice: [],
			otherFactor: 'Yếu tố khác',
			showOtherFactor: false,
			arrayTotalViolationArea: {},
			arrayTotalAppropriateArea: {},
			detail1: null,
			detail2: null,
			detail3: null,
			total_appraise_area: 0,
			total_vio_appraise_area: 0,
			dgxd1: 0,
			dgxd2: 0,
			dgxd3: 0,
			clcl1: 0,
			clcl2: 0,
			clcl3: 0,
			// Tổng giá trị tài sản ước tính
			totalPriceEstimate1: 0,
			totalPriceEstimate2: 0,
			totalPriceEstimate3: 0,
			// đơn giá đất vi phạm quy hoạch
			totalPriceViolationArea1: 0,
			totalPriceViolationArea2: 0,
			totalPriceViolationArea3: 0,
			// Chi phí chuyển mục đích sử dụng
			price1: 0,
			price2: 0,
			price3: 0,
			totalPrice1: 0,
			totalPrice2: 0,
			totalPrice3: 0,
			dgd1: 0,
			dgd2: 0,
			dgd3: 0,
			totalPricePL1: 0,
			totalPricePL2: 0,
			totalPricePL3: 0,
			priceHdd1: 0,
			priceHdd2: 0,
			priceHdd3: 0,
			priceKcd1: 0,
			priceKcd2: 0,
			priceKcd3: 0,
			priceKd1: 0,
			priceKd2: 0,
			priceKd3: 0,
			priceDkht1: 0,
			priceDkht2: 0,
			priceDkht3: 0,
			pricePt1: 0,
			pricePt2: 0,
			pricePt3: 0,
			priceDktt1: 0,
			priceDktt2: 0,
			priceDktt3: 0,
			priceQm1: 0,
			priceQm2: 0,
			priceQm3: 0,
			priceCrmt1: 0,
			priceCrmt2: 0,
			priceCrmt3: 0,
			priceCskd1: 0,
			priceCskd2: 0,
			priceCskd3: 0,
			priceDrd1: 0,
			priceDrd2: 0,
			priceDrd3: 0,
			mgcd1: 0,
			mgcd2: 0,
			mgcd3: 0,
			mgcdMin: 0,
			mgcdMax: 0,
			mgtb: 0,
			mgtn: 0,
			mgtnTemp: 0,
			tldc1: 0,
			tldc2: 0,
			tldc3: 0,
			baseUnitPrice: 0,
			baseAcronym: '',
			pricePl1: 0,
			pricePl2: 0,
			pricePl3: 0,
			mgcl1: 0,
			mgcl2: 0,
			mgcl3: 0,
			priceGt1: 0,
			priceGt2: 0,
			priceGt3: 0,
			priceAnmts1: 0,
			priceAnmts2: 0,
			priceAnmts3: 0,
			priceQh1: 0,
			priceQh2: 0,
			priceQh3: 0,
			dtsxd1: 0,
			dtsxd2: 0,
			dtsxd3: 0,
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
			asset_data_price: [],
			data_land_price: [],
			data_land_price_demo: [],
			area_adjusted: {},
			round_total: 0,
			layer_cutting_procedure: false,
			layer_cutting_procedure_price: 0,
			round_layer_cutting_produre: 0,
			round_composite: 0,
			round_violation_facility: 0,
			round_violation_composite: 0,
			violation_facility_price: 0,
			violation_composite_price: 0,
			show_violation_price: false,
			appropriate_zoning_land: [],
			violation_zoning_land: [],
			asset_appropriate_area_arr: [],
			checkValidArea: false,
			totalPriceAppropriateAppraiseFacility: 0,
			totalPriceAppropriateAppraiseRemaining: 0,
			totalPriceViolationAppraiseFacility: 0,
			totalPriceViolationAppraiseRemaining: 0,
			result_price_appropriate_facility: 0,
			result_price_appropriate_remaining: 0,
			result_price_violation_facility: 0,
			result_price_violation_remaining: 0,
			showWarningSave: false,
			dataTab2: {},
			openPrint: false,
			reportData: [],
			appraisal_methods: [],
			purposePrice: [],
			violatePrice: [],
			mainPrice: {},
			totalPrice: {
				land_asset_price: 0,
				tangible_asset_price: 0,
				other_asset_price: 0,
				total_asset_price: 0,
				total_asset_round: 0
			},
			principleConfig: [],
			render_construction: 92321,
			adjustPriceData: {}
		}
	},
	created () {

	},
	mounted () {
		this.principleConfig = this.jsonConfig.principle
		if (this.form && this.form.asset_general) {
			this.env = process.env.CLIENT_ENV
			this.getOtherComparison()
			this.getDataLand()
			this.calculation(this.form)
			this.getData()
			this.getDataPrice()
			this.appraisal_methods = this.form.appraisal_methods
			this.purposePrice = this.getPurposeLandType()
			this.violatePrice = this.getViolateLandType()
			this.mainPrice = this.getMainLandType()
			this.totalPrice = this.getTotalPrice()
			this.setDefaultConstructionData()
			this.key_render_around += 1
		}
	},

	computed: {
		getTotalArea () {
			let area = 0
			if (this.appraises.properties && this.appraises.properties.length > 0 && this.appraises.properties[0].appraise_land_sum_area) {
				area = parseFloat(this.appraises.properties[0].appraise_land_sum_area).toFixed(2)
			}
			return area
		},
		mainArea () {
			return this.mainPrice.area ? this.mainPrice.area.toFixed(2) : 0
		},
		getMainPrice () {
			let mainPrice = this.mainPrice
			mainPrice.price = this.mgtn
			let price = this.roundPrice(mainPrice.price, 0)
			return price
		},
		calMainRoundPrice () {
			let mainPrice = this.mainPrice
			let price = this.roundPrice(mainPrice.islayerCuttingPirce ? mainPrice.layerCuttingPirce : mainPrice.price, mainPrice.round)
			if (this.purposePrice.length > 0) { this.calPurposePrice(price, mainPrice.ubndPrice) }
			if (this.violatePrice.length > 0) { this.calViolatePrice(this.violatePrice.find(i => i.isMain === true), price) }
			return price
		},
		calTotalLandPrice () {
			let price = 0
			// console.log(this.calTotalPurposePrice, this.calTotalViolatePrice, this.calMainTotalPrice)
			// price = Number(this.calTotalPurposePrice) + Number(this.calTotalViolatePrice) + Number(this.calMainTotalPrice)
			const mainPrice = this.calMainTotalPrice
			const violatePrice = this.calTotalViolatePrice
			const purposePrice = this.calTotalPurposePrice
			// console.log(mainPrice, purposePrice, violatePrice)
			price = mainPrice + violatePrice + purposePrice
			return price || 0
		},
		calTotalPurposePrice () {
			let price = 0
			let purpose = this.purposePrice
			purpose.forEach(i => {
				price = price + this.roundPrice(i.price, i.round) * i.area
			})
			return price || 0
		},
		calTotalViolatePrice () {
			let price = 0
			let violate = this.violatePrice
			violate.forEach(i => {
				price = price + this.roundPrice(i.price, i.round) * i.area
			})
			return price || 0
		},
		calMainTotalPrice () {
			let price = 0
			price = price + this.roundPrice(this.calMainRoundPrice * this.mainPrice.area)
			return price || 0
		},
		calTangibleAssetPrice () {
			return this.form.price_tangible_asset ? this.form.price_tangible_asset : 0
		},
		calOtherAssetPrice () {
			return this.form.price_other_asset ? this.form.price_other_asset : 0
		},
		calTotalPrice () {
			let totalPrice = this.totalPrice
			let price = totalPrice.land_asset_price + totalPrice.tangible_asset_price + totalPrice.other_asset_price
			return price
		}
	},
	watch: {
		calTotalLandPrice: {
			deep: true,
			handler (newValue) {
				this.setPriceByType(newValue, 'land_asset_price')
			}
		}
	},
	methods: {
		setDefaultConstructionData () {
			let appraisal_methods = this.form.appraisal_methods
			let constructionRemainQuality = this.constructionRemainQuality
			let constructionInAppraisal = appraisal_methods.find(i => toLower(i.slug) === 'xac_dinh_chat_luong_con_lai')
			if (constructionInAppraisal) {
				constructionRemainQuality.forEach(item => {
					if (toLower(item.slug) === toLower(constructionInAppraisal.slug_value)) {
						item.is_defaults = true
					} else {
						item.is_defaults = false
					}
				})
			}
			let constructionPriceType = this.constructionPriceType
			let priceTypeInAppraisal = appraisal_methods.find(i => toLower(i.slug) === 'xac_dinh_don_gia_xay_dung')
			if (priceTypeInAppraisal) {
				constructionPriceType.forEach(item => {
					if (toLower(item.slug) === toLower(priceTypeInAppraisal.slug_value)) {
						item.is_defaults = true
					} else {
						item.is_defaults = false
					}
				})
			}
			this.render_construction += 1
		},

		getTransactionTime (transactionType, time) {
			let strTime = '-'
			// console.log(transactionType.description)
			if (transactionType && transactionType.description === 'ĐÃ BÁN') {
				if (time) {
					try {
						const d = new Date(time)
						strTime = 'Tháng ' + d.getMonth() + '/' + d.getFullYear()
					} catch (err) {
						throw err
					}
				}
			}
			return strTime
		},
		setPriceByType (value, type = 'land_asset_price') {
			this.totalPrice[type] = value
			this.totalPrice = this.getTotalPrice()
		},
		getTotalPrice () {
			let data = {
				land_asset_price: this.calTotalLandPrice,
				tangible_asset_price: this.calTangibleAssetPrice,
				other_asset_price: this.calOtherAssetPrice,
				total_asset_price: this.calTotalPrice
			}
			return data
		},
		calPurposePriceByIndex (index) {
			let purpose = this.purposePrice[index]
			let price = this.roundPrice(purpose.price, purpose.round)
			let violate = this.violatePrice.find(item => item.acronym === purpose.acronym)
			if (violate) { this.calViolatePrice(violate, price) }
			return price
		},
		calPurposePrice (price, basePrice) {
			let slug = this.appraisal_methods.find(i => i.slug === 'tinh_gia_dat_hon_hop_con_lai')
			let slugValue = slug.slug_value
			if (slug && slugValue !== 'theo-phuong-phap-doc-lap') {
				let value = slug.value
				this.purposePrice.forEach(i => {
					let diffUBND = basePrice - i.ubndPrice
					let calPrice = 0
					if (slugValue === 'theo-chi-phi-chuyen-mdsd-dat') {
						calPrice = price - diffUBND
					} else {
						calPrice = (price * value / 100).toFixed(0)
					}
					let violate = this.violatePrice.find(item => item.acronym === i.acronym)
					if (violate) { this.calViolatePrice(violate, calPrice) }
					i.price = calPrice
				})
			}
		},
		calViolatePrice (violate, price) {
			if (violate) {
				let slug = this.appraisal_methods.find(i => i.slug === 'tinh_gia_dat_vi_pham_quy_hoach')
				if (slug) {
					let value = slug.value
					let slugValue = slug.slug_value
					if (slugValue === 'theo-gia-dat-qd-ubnd') {
						violate.price = violate.ubndPrice
					} else if (slugValue === 'theo-ty-le-gia-dat-thi-truong') {
						violate.price = (price * value / 100).toFixed(0)
					}
				}
			}
		},
		changeDynamicViolateRound (event, index) {
			let data = this.violatePrice[index]
			data.round = event
		},
		changeDynamicViolatePrice (event, index) {
			let data = this.violatePrice[index]
			data.price = event
		},
		changeDynamicPurposeRound (event, index) {
			let data = this.purposePrice[index]
			data.round = event
		},
		changeDynamicPurposePrice (event, index) {
			this.purposePrice[index].price = event
		},
		errorDynamicPurposePrice (event, index) {
			if (event) {
				this.purposePrice[index].price = event
			} else this.purposePrice[index].price = 0
		},
		getMainLandType () {
			let item = this.form.properties[0].property_detail.find(i => i.is_transfer_facility === true)
			let data = {}
			if (item) {
				let slug = 'land_asset_purpose_' + item.land_type_purpose.acronym
				let slug_price = slug + '_price'
				let slug_round = slug + '_round'
				// let landPriceData = this.form.asset_price.find(i => i.slug === 'land_asset_purpose_' + item.land_type_purpose.acronym + '_price')
				let roundData = this.form.asset_price.find(i => i.slug === slug_round)
				// let layerCuttingPirceData = this.form.asset_price.find(i => i.slug === 'layer_cutting_price')
				// let islayerCuttingPirceData = this.appraisal_methods.find(i => i.slug === 'layer_cutting_procedure')
				// let price = landPriceData ? landPriceData.value : 0
				// let islayerCuttingPirce = islayerCuttingPirceData ? islayerCuttingPirceData.value : false
				// let layerCuttingPirce = layerCuttingPirceData ? layerCuttingPirceData.value : 0

				let round = roundData ? roundData.value : 0
				let price = this.mgtb
				let islayerCuttingPirce = this.layer_cutting_procedure
				let layerCuttingPirce = this.layer_cutting_procedure_price
				data = {
					acronym: item.land_type_purpose.acronym,
					area: item.main_area,
					price: price,
					round: round,
					islayerCuttingPirce: islayerCuttingPirce,
					layerCuttingPirce: layerCuttingPirce,
					ubndPrice: item.circular_unit_price,
					slug_price: slug_price,
					slug_round: slug_round,
					isError: false
				}
			}
			return data
		},
		getPurposeLandType () {
			let detail = this.form.properties[0].property_detail.filter(i => i.is_transfer_facility === false && i.main_area > 0)
			let data = []
			if (detail.length > 0) {
				detail.forEach(item => {
					let slug = 'land_asset_purpose_' + item.land_type_purpose.acronym
					let slug_price = slug + '_price'
					let slug_round = slug + '_round'
					let landPriceData = this.form.asset_price.find(i => i.slug === slug_price)
					let roundData = this.form.asset_price.find(i => i.slug === slug_round)
					let price = landPriceData ? landPriceData.value : 0
					let round = roundData ? roundData.value : 0
					data.push({
						acronym: item.land_type_purpose.acronym,
						area: item.main_area,
						price: price,
						round: round,
						ubndPrice: item.circular_unit_price,
						slug_price: slug_price,
						slug_round: slug_round
					})
				})
			}
			return data
		},
		getViolateLandType () {
			let detail = this.form.properties[0].property_detail.filter(i => i.planning_area > 0)
			let data = []
			if (detail.length > 0) {
				detail.forEach(item => {
					let slug = 'land_asset_purpose_' + item.land_type_purpose.acronym + '_violation'
					let slug_price = slug + '_price'
					let slug_round = slug + '_round'
					let landPriceData = this.form.asset_price.find(i => i.slug === slug_price)
					let roundData = this.form.asset_price.find(i => i.slug === slug_round)
					let price = landPriceData ? landPriceData.value : 0
					let round = roundData ? roundData.value : 0
					data.push({
						acronym: item.land_type_purpose.acronym,
						area: item.planning_area,
						price: price,
						round: round,
						ubndPrice: item.circular_unit_price,
						isMain: item.is_transfer_facility,
						slug_price: slug_price,
						slug_round: slug_round
					})
				})
			}
			return data
		},
		getComparisonTitleByType (data, type, isAsset = true) {
			let factor = data.find(item => item.type === type)
			// console.log(data, type, factor, isAsset)
			if (factor) {
				if (isAsset) { return factor.asset_title } else { return factor.appraise_title }
			} else {
				return '-'
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
				return this.formatNumber((tangible_assets.unit_price_m2 * tangible_assets.total_construction_base) * tangible_assets.remaining_quality / 100) + 'đ'
			} else return '-'
		},
		changePercentSaleRating (event, index) {
			if (event) {
				this.form.appraise_adapter[index].percent = event
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
			// set giá trị default làm tròn đất cơ sở
			if (+this.form.round_total || +this.form.round_total === 0) {
				this.round_total = this.form.round_total
			} else this.round_total = 0
			// set giá trị default làm tròn đất vi phạm quy hoạch cơ sở
			if (+this.form.round_violation_facility || +this.form.round_violation_facility === 0) {
				this.round_violation_facility = this.form.round_violation_facility
			} else this.round_violation_facility = 0
			// set giá trị default làm tròn đất hỗn hợp còn lại
			if (+this.form.round_composite || +this.form.round_composite === 0) {
				this.round_composite = this.form.round_composite
			} else this.round_composite = 0
			// set giá trị default làm tròn đất vi phạm quy hoạch hỗn hợp còn lại
			if (+this.form.round_violation_composite || +this.form.round_violation_composite === 0) {
				this.round_violation_composite = this.form.round_violation_composite
			} else this.round_violation_composite = 0

			// set giá trị phương pháp cắt lớp
			if (this.form.layer_cutting_procedure === true) {
				this.layer_cutting_procedure = true
				if (this.form.layer_cutting_price) {
					this.layer_cutting_procedure_price = this.form.layer_cutting_price
				}
			}

			// set giá đất hỗn hợp còn lại
			if (this.form.remaining_price) {
				this.remaining_commerce_price = this.form.remaining_price.remaining_commerce_price
			}
			this.key_render_around += 1
			if (this.form.properties[0].property_detail && this.form.properties[0].property_detail.length > 1) {
				this.type_is_not_facility = this.form.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).land_type_purpose.acronym
			}
			// set config render data
			this.appraises = {}
			let appraise = JSON.parse(JSON.stringify(this.form))
			let asset_generals = []
			let full_address_appraise = ''
			let arrayPropertyChoosing = []
			if (appraise.street && appraise.ward && appraise.district && appraise.province) {
				full_address_appraise = `${appraise.street.name}, ${appraise.ward.name}, ${appraise.district.name}, ${appraise.province.name}`
			}
			if (typeof appraise.asset_general !== 'undefined') {
				appraise.comparison_factor.forEach((data, index) => {
					if (data.type === 'yeu_to_khac' && data.asset_title === '' && data.appraise_title === '') {
						appraise.comparison_factor[index].asset_title = 'Không biết'
						appraise.comparison_factor[index].appraise_title = 'Không biết'
					}
				})
				appraise.asset_general.forEach(asset_general => {
					// asset_general.adjust_percent = 100 + +asset_general.adjust_percent
					const comparison_factor_TSSS = appraise.comparison_factor.filter(comparison => comparison.asset_general_id === asset_general.id)
					asset_generals.push({
						id: asset_general.id,
						comparison_factor: comparison_factor_TSSS
					})
					asset_general.properties.forEach(property => {
						let appraiseHasAsset = appraise.appraise_has_assets.filter(item => item.asset_general_id === asset_general.id && item.asset_property_detail_id === property.id)
						if (appraiseHasAsset && appraiseHasAsset.length > 0) {
							arrayPropertyChoosing.push(property)
						}
					})
				})
				this.appraises = {
					id: appraise.id,
					coordinates: appraise.coordinates,
					propertyChoosing: arrayPropertyChoosing,
					comparison_factor: asset_generals,
					asset_general: appraise.asset_general,
					asset_type: appraise.asset_type,
					appraise_has_assets: appraise.appraise_has_assets,
					properties: appraise.properties,
					arrayCPMDSD: appraise.cpcmdsd,
					arrayDGBQ: appraise.dgbq,
					tangible_assets: appraise.tangible_assets,
					full_address_appraise: full_address_appraise
				}
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
						appraise_title: item.appraise_title,
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
		getDataLand () {
			// dat phu hop quy hoach : appropriate zoning land
			// dat vi pham quy hoach : violation zoning land
			let formDataClone = JSON.parse(JSON.stringify(this.form))
			// tao data tong property detail cua asset_general
			formDataClone.asset_general.forEach(asset => {
				asset.properties.forEach(property_asset => {
					let appraiseHasAsset = formDataClone.appraise_has_assets.filter(item => item.asset_property_detail_id === property_asset.id)
					if (appraiseHasAsset && appraiseHasAsset.length > 0) {
						property_asset.property_detail.forEach(property_asset_detail => {
							// lay gia tri dat vi pham quy hoach tu asset_unit_area
							let getDataUnitPrice = this.form.asset_unit_area.filter(itemVioLand => itemVioLand.asset_general_id === property_asset.asset_general_id && itemVioLand.land_type_id === property_asset_detail.land_type_purpose)
							// push dien tich dat phu hop quy hoach
							if (getDataUnitPrice && getDataUnitPrice.length > 0) {
								this.asset_appropriate_area_arr.push({
									asset_general_id: property_asset.asset_general_id,
									total_area: parseFloat((property_asset_detail.total_area - +getDataUnitPrice[0].violation_asset_area).toFixed(2)),
									name_purpose_land_asset: property_asset_detail.land_type_purpose_data.acronym
								})
							}
						})
					}
				})
			})
			if (formDataClone.properties && formDataClone.properties[0] && formDataClone.properties[0].property_detail && formDataClone.properties[0].property_detail.length > 0) {
				formDataClone.properties[0].property_detail.forEach(property_appraise => {
					// lay dien tich phu hop quy hoach (TSTD)
					this.appropriate_zoning_land.push({
						name_purpose_land: property_appraise.land_type_purpose.acronym,
						appropriate_appraise_land: Number((+property_appraise.total_area - +property_appraise.planning_area).toFixed(2)),
						asset_general_land: []
					})
					// lay dien tich vi pham quy hoach (TSTD)
					this.violation_zoning_land.push({
						name_purpose_land: property_appraise.land_type_purpose.acronym,
						violation_appraise_land: +property_appraise.planning_area,
						asset_general_land: []
					})
					this.total_appraise_area += Number((+property_appraise.total_area - +property_appraise.planning_area).toFixed(2))
					this.total_vio_appraise_area += +property_appraise.planning_area
				})
			}
			// them du lieu hien thi dat phu hop quy hoach (TSSS)
			const map = new Map()
			this.asset_appropriate_area_arr.forEach(itemLandAsset => {
				if (!map.has(itemLandAsset.name_purpose_land_asset)) {
					map.set(itemLandAsset.name_purpose_land_asset)
					let dataLand = this.asset_appropriate_area_arr.filter(item => item.name_purpose_land_asset === itemLandAsset.name_purpose_land_asset)
					// check ton tai trong dat phu hop quy hoach
					let checkExist = this.appropriate_zoning_land.filter(checkItem => checkItem.name_purpose_land === itemLandAsset.name_purpose_land_asset)

					if (checkExist && checkExist.length > 0) {
						// them muc dat cua TSSS vao muc dich dat da ton tai
						this.appropriate_zoning_land.forEach((appropriateLand, index) => {
							if (appropriateLand.name_purpose_land === itemLandAsset.name_purpose_land_asset) {
								this.appropriate_zoning_land[index]['asset_general_land'] = dataLand
							}
						})
					} else {
						// them muc dat cua TSSS co ma TSTD khong co
						this.appropriate_zoning_land.push({
							name_purpose_land: itemLandAsset.name_purpose_land_asset,
							appropriate_appraise_land: 0,
							asset_general_land: dataLand
						})
					}
				}
			})
			// them du lieu dat vi pham quy hoach (TSSS)
			const map1 = new Map()
			this.form.asset_unit_area.forEach(asset_data => {
				if (!map1.has(asset_data.land_type_id)) {
					map1.set(asset_data.land_type_id)
					const dataViolationLand = this.form.asset_unit_area.filter(item => item.land_type_id === asset_data.land_type_id)
					// check ton tai trong dat vi pham quy hoach
					let checkExist = this.violation_zoning_land.filter(checkItem => checkItem.name_purpose_land === asset_data.land_type_data.acronym)
					if (checkExist && checkExist.length > 0) {
						this.violation_zoning_land.forEach((violationLand, index) => {
							if (violationLand.name_purpose_land === asset_data.land_type_data.acronym) {
								this.violation_zoning_land[index]['asset_general_land'] = dataViolationLand
							}
						})
					} else {
						// them muc data cua TSSS co ma TSTD khong co
						this.violation_zoning_land.push({
							name_purpose_land: asset_data.land_type_data.acronym,
							violation_appraise_land: 0,
							asset_general_land: dataViolationLand
						})
					}
				}
			})
		},
		getDataPrice () {
			this.form.asset_unit_price.forEach((item, index) => {
				if (!item.update_value) {
					this.form.asset_unit_price[index].update_value = item.original_value
				}
			})
			let formDataClone = JSON.parse(JSON.stringify(this.form))
			/// /////////// resolve data price UBND
			const map1 = new Map()
			const map2 = new Map()
			formDataClone.properties.forEach(data_property => {
				data_property.property_detail.forEach(data_property_detail => {
					if (data_property_detail.is_transfer_facility) {
						this.data_land_price_demo.push({
							purpose_land: data_property_detail.land_type_purpose.acronym,
							price_appraise: data_property_detail.circular_unit_price,
							facility: true,
							price_asset: []
						})
					} else {
						this.data_land_price_demo.push({
							purpose_land: data_property_detail.land_type_purpose.acronym,
							price_appraise: data_property_detail.circular_unit_price,
							facility: false,
							price_asset: []
						})
					}
				})
			})
			this.form.asset_unit_price.forEach((itemUnitPrice) => {
				if (!map1.has(itemUnitPrice.land_type_id)) {
					map1.set(itemUnitPrice.land_type_id, true)
					this.data_land_price_demo.forEach((itemLandPrice, index) => {
						if (itemUnitPrice.land_type_data.acronym === itemLandPrice.purpose_land) {
							let get_data_filter = this.form.asset_unit_price.filter(item_filter => item_filter.land_type_data.acronym === itemLandPrice.purpose_land)
							this.data_land_price_demo[index].price_asset.push(...get_data_filter)
						}
					})
				}
			})
			this.form.asset_unit_price.forEach((itemUnitPrice) => {
				if (!map2.has(itemUnitPrice.land_type_id)) {
					map2.set(itemUnitPrice.land_type_id, true)
					let checkDataUnitExist = this.data_land_price_demo.filter(item => item.purpose_land === itemUnitPrice.land_type_data.acronym)
					if (checkDataUnitExist && checkDataUnitExist.length === 0) {
						let get_data_filter = this.form.asset_unit_price.filter(item_filter => item_filter.land_type_data.acronym === itemUnitPrice.land_type_data.acronym)
						this.data_land_price_demo.push({
							purpose_land: itemUnitPrice.land_type_data.acronym,
							price_appraise: null,
							facility: false,
							price_asset: get_data_filter
						})
					}
				}
			})
			this.key_render_around += 1
		},
		handleSaveTab1 (tab1) {
			// let check = this.isEditStatus
			// if (check !== '') {
			// 	this.openMessage(check)
			// 	return
			// }

			const dataSave = []
			const otherDataSave = null
			const dataDelete = null
			const asset_unit_price = this.form.asset_unit_price
			const asset_unit_area = this.form.asset_unit_area
			const round_total = null
			const round_composite = null
			const round_violation_composite = null
			const round_violation_facility = null
			const appraise_adapter = this.form.appraise_adapter
			const layer_cutting_procedure = null
			const layer_cutting_procedure_price = null
			// check area violation
			let checkArea = false
			let checkTypeArea = false
			this.asset_appropriate_area_arr.forEach(itemLandAsset => {
				if (itemLandAsset.total_area < 0) {
					checkTypeArea = true
				}
			})
			this.form.asset_general.forEach(item => {
				if (this.arrayTotalAppropriateArea[item.id] <= 0) {
					checkArea = true
				}
			})
			// check price
			let checkPrice = false
			this.form.asset_unit_price.forEach(item_price => {
				if (item_price.update_value < 0) {
					checkPrice = true
				}
			})

			let checkViolatePrice = false
			appraise_adapter.forEach(item_price => {
				if (item_price.change_violate_price < 0) {
					checkViolatePrice = true
				}
			})
			const payloadData = {
				comparison_factor: dataSave,
				other_comparison: otherDataSave,
				delete_other_comparison: dataDelete,
				asset_unit_price: asset_unit_price,
				asset_unit_area: asset_unit_area,
				round_total: +round_total,
				round_composite: +round_composite,
				round_violation_composite: +round_violation_composite,
				round_violation_facility: +round_violation_facility,
				appraise_adapter: appraise_adapter,
				layer_cutting_procedure: layer_cutting_procedure,
				layer_cutting_price: +layer_cutting_procedure_price,
				remaining_price: null,
				main_price: this.mainPrice,
				purpose_price: this.purposePrice,
				violate_price: this.violatePrice
			}
			if (checkArea) {
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch phải lớn hơn 0',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkTypeArea) {
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkPrice) {
				this.$toast.open({
					message: 'Đơn giá UBND không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkViolatePrice) {
				this.$toast.open({
					message: 'Giá trị vi phạm quy hoạch không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else this.handleSaveSummarization(payloadData)
		},
		async handleSaveSummarization (payloadData) {
			this.isSubmit = true
			const res = await CertificateAsset.submitStep7(payloadData, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu bảng tổng hợp thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.form.price_land_asset = res.data.price_land_asset
				this.form.price_other_asset = res.data.price_other_asset
				this.form.price_tangible_asset = res.data.price_tangible_asset
				this.form.price_total_asset = res.data.price_total_asset
				this.form.round_appraise_total = res.data.round_appraise_total
				this.key_render_5 += 1
				this.$emit('updateDataStep7')
				this.isSubmit = false
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		// ---------------------------------------------- TAB_2 --------------------------------------------------------------------//
		showPriceApproriateFacility (property) {
			if (property) {
				let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
				let area = +facilityProperty.main_area
				let price = 0
				if (this.layer_cutting_procedure) {
					price = this.formatCurrent(this.round_layer_cutting_produre)
				} else {
					price = this.formatCurrent(parseFloat(this.mgtn).toFixed(0))
				}
				this.result_price_appropriate_facility = (price * area).toFixed(0)
				return this.result_price_appropriate_facility
			} else return 0
		},
		showPriceApproriateRemaining (property) {
			if (property) {
				let remaingProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === false)
				let remaining_price = this.formatRoundComposite(this.remaining_commerce_price)
				let area = +remaingProperty.total_area - +remaingProperty.planning_area
				this.result_price_appropriate_remaining = (remaining_price * area).toFixed(0)
				return this.result_price_appropriate_remaining
			} else return 0
		},
		showPriceViolationFacility (property) {
			if (property) {
				let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
				let vio_facility_price = this.formatRoundViolationFacility(this.violation_facility_price)
				let area = +facilityProperty.planning_area
				this.result_price_violation_facility = (vio_facility_price * area).toFixed(0)
				return this.result_price_violation_facility
			} else return 0
		},
		showPriceViolationRemaining (property) {
			if (property) {
				let remaingProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === false)
				let vio_remaining_price = this.formatRoundViolationComposite(this.violation_composite_price)
				let area = +remaingProperty.planning_area
				this.result_price_violation_remaining = (vio_remaining_price * area).toFixed(0)
				return this.result_price_violation_remaining
			} else return 0
		},
		showApproriateArea (property) {
			if (property) {
				let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
				if (facilityProperty) {
					// return facilityProperty.total_area - facilityProperty.planning_area
					return facilityProperty.main_area
				} else return 0
			} else return 0
		},
		showRemainingArea (property) {
			if (property) {
				let violationProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === false)
				if (violationProperty) {
					return violationProperty.total_area - violationProperty.planning_area
				} else return 0
			} else return 0
		},
		showTotal () {
			let result_price_appropriate_facility = +this.result_price_appropriate_facility
			let result_price_appropriate_remaining = +this.result_price_appropriate_remaining
			let result_price_violation_facility = +this.result_price_violation_facility
			let result_price_violation_remaining = +this.result_price_violation_remaining
			let total = result_price_appropriate_facility + result_price_appropriate_remaining + result_price_violation_facility + result_price_violation_remaining
			return total
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
		formatRoundComposite (value) {
			if (this.round_composite && this.round_composite > 0 && this.round_composite <= 7) {
				let round = Math.pow(10, this.round_composite)
				return Math.ceil(value / round) * round
			} else if (this.round_composite && this.round_composite < 0 && this.round_composite >= -7) {
				let round = Math.pow(10, Math.abs(this.round_composite))
				return Math.floor(value / round) * round
			} else return value
		},
		formatRoundViolationFacility (value) {
			if (this.round_violation_facility && this.round_violation_facility > 0 && this.round_violation_facility <= 7) {
				let round = Math.pow(10, this.round_violation_facility)
				return Math.ceil(value / round) * round
			} else if (this.round_violation_facility && this.round_violation_facility < 0 && this.round_violation_facility >= -7) {
				let round = Math.pow(10, Math.abs(this.round_violation_facility))
				return Math.floor(value / round) * round
			} else return value
		},
		formatRoundViolationComposite (value) {
			if (this.round_violation_composite && this.round_violation_composite > 0 && this.round_violation_composite <= 7) {
				let round = Math.pow(10, this.round_violation_composite)
				return Math.ceil(value / round) * round
			} else if (this.round_violation_composite && this.round_violation_composite < 0 && this.round_violation_composite >= -7) {
				let round = Math.pow(10, Math.abs(this.round_violation_composite))
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
		async changePriceLand (event, dataItem, indexPrice) {
			await this.form.asset_unit_price.forEach(item => {
				if (item.asset_general_id === dataItem.asset_general_id && item.land_type_id === dataItem.land_type_id) { item.update_value = event }
				item.update = 2
			})
			await this.form.asset_general.forEach(assetGeneral => {
				if (assetGeneral.id === dataItem.asset_general_id) {
					assetGeneral.properties.forEach(property => {
						property.property_detail.forEach(assetDetail => {
							if (assetDetail.land_type_purpose === dataItem.land_type_id) {
								assetDetail.circular_unit_price = event
							}
						})
					})
				}
			})
			if (indexPrice || indexPrice === 0) {
				await this.calculationViolationPrice(this.form, indexPrice)
				await this.calculationChangePrice(this.form, indexPrice)
			}
			await this.calculation(this.form)
			this.key_render_1 += 1
		},
		async changeViolationLand (event, dataItemLand, indexArea) {
			this.form.asset_unit_area.forEach(item => {
				if (item.asset_general_id === dataItemLand.asset_general_id && item.land_type_id === dataItemLand.land_type_id) {
					if (+event) {
						item.violation_asset_area = parseFloat(+event).toFixed(2)
					} else item.violation_asset_area = 0
				}
			})
			if (indexArea || indexArea === 0) {
				await this.calculationViolationPrice(this.form, indexArea)
				await this.calculationChangePrice(this.form, indexArea)
			}
			await this.calculation(this.form)
			this.key_render_1 += 1
		},
		changeViolationPrice (event, index) {
			if (event) {
				this.form.appraise_adapter[index].change_violate_price = event
			} else this.form.appraise_adapter[index].change_violate_price = 0
			this.calculation(this.form)
		},
		errorViolationPrice (event, index) {
			if (event) {
				this.form.appraise_adapter[index].change_violate_price = event
			} else this.form.appraise_adapter[index].change_violate_price = 0
		},
		changePurposePrice (event, index) {
			if (event) {
				this.form.appraise_adapter[index].change_purpose_price = event
			} else this.form.appraise_adapter[index].change_purpose_price = 0
			this.calculation(this.form)
		},
		async dialogDeleteComparisionDefault (type) {
			this.openMdodalDeleteDefault = true
			this.typeComparision = type
		},
		async actionDeleteComparisionDefault () {
			let tempArrayComparison = []
			await this.appraises.comparison_factor.forEach(data => {
				data.comparison_factor.forEach(item => {
					if (item.type === this.typeComparision) {
						item.status = 0
						item.adjust_percent = 0
					}
					tempArrayComparison.push(item)
				})
			})
			this.form.comparison_factor = tempArrayComparison
			this.calculation(this.form)
		},
		changeLegalRate (e, indexAsset, type) {
			let tempArrayComparison = []
			let factor = this.appraises.comparison_factor[indexAsset].comparison_factor.find(i => i.type === type)
			if (factor) {
				if (e) {
					factor.percent = e
				} else {
					factor.percent = 0
				}
			}
			this.appraises.comparison_factor.forEach(data => {
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
			await this.form.asset_general.forEach(asset => {
				// tạo data gửi
				this.other_comparison.push({
					adjust_percent: 0,
					appraise_id: data.id,
					appraise_title: 'Không biết',
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
						appraise_title: item.appraise_title,
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
		changeLayerCuttingProcedure (event) {
			this.mainPrice.islayerCuttingPirce = event
			this.mainPrice.isError = false
			if (event === true) { this.mainPrice.layerCuttingPirce = parseInt(this.mainPrice.price.toFixed(0)) } else { this.mainPrice.layerCuttingPirce = 0 }
			// if (event) {
			// 	if (this.form.layer_cutting_price) {
			// 		this.layer_cutting_procedure_price = this.form.layer_cutting_price
			// 	}
			// 	this.round_layer_cutting_produre = this.layer_cutting_procedure_price
			// 	this.mgtnTemp = this.layer_cutting_procedure_price
			// }
			// this.calculation(this.form)
			// this.key_render_around += 1
		},
		changeLayerCuttingPriceError (event) {
			this.mainPrice.isError = event
		},
		changeLayerCuttingPrice (event) {
			if (event) {
				this.mainPrice.isError = false
				this.mainPrice.layerCuttingPirce = parseInt(event.toFixed(0))
			} else {
				this.mainPrice.layerCuttingPirce = 0
				this.mainPrice.isError = true
			}
			// if (event) {
			// 	this.layer_cutting_procedure_price = parseFloat(event).toFixed(0)
			// } else this.layer_cutting_procedure_price = ''
			// this.mgtnTemp = this.layer_cutting_procedure_price ? this.layer_cutting_procedure_price : 0
			// this.calculation(this.form)
			// this.key_render_around += 1
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
					item.appraise_title = event
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
				this.mainPrice.round = parseInt(event)
				// this.round_total = parseFloat(event).toFixed(0)
				// this.mgtnTemp = this.formatCurrent(this.mgtn)
			} else {
				this.mainPrice.round = 0
				// this.mgtnTemp = this.mgtn
			}
			// this.calculation(this.form)
			// this.key_render_around += 1
		},
		changeRemaingCommercePrice (event) {
			if (event) {
				this.remaining_commerce_price = event
			} else this.remaining_commerce_price = ''
		},
		// lam tron dat hon hop con lai
		changeRoundCompositeLand (event) {
			if (event || event === 0) {
				this.round_composite = parseFloat(event).toFixed(0)
				this.mgtnTemp = this.formatCurrent(this.mgtn)
			} else {
				this.round_composite = 0
				this.mgtnTemp = this.mgtn
			}
			this.calculation(this.form)
		},
		// lam tron dat vi pham quy hoach co so
		changeRoundViolationFacility (event) {
			if (event || event === 0) {
				this.round_violation_facility = parseFloat(event).toFixed(0)
				this.mgtnTemp = this.formatCurrent(this.mgtn)
			} else {
				this.round_violation_facility = 0
				this.mgtnTemp = this.mgtn
			}
			this.calculation(this.form)
		},
		// lam tron dat vi pham quy hoach con lai
		changeRoundViolationComposite (event) {
			if (event || event === 0) {
				this.round_violation_composite = parseFloat(event).toFixed(0)
				this.mgtnTemp = this.formatCurrent(this.mgtn)
			} else {
				this.round_violation_composite = 0
				this.mgtnTemp = this.mgtn
			}
			this.calculation(this.form)
		},
		handleSaveTab2 () {
			// let check = this.isEditStatus
			// if (check !== '') {
			// 	this.openMessage(check)
			// 	return
			// }
			const dataSave = []
			const asset_unit_price = this.form.asset_unit_price
			const asset_unit_area = this.form.asset_unit_area
			const appraise_adapter = this.form.appraise_adapter
			const otherDataSave = this.other_comparison
			const dataDelete = this.delete_other_comparison
			const round_total = this.round_total
			const round_composite = this.round_composite
			const round_violation_composite = this.round_violation_composite
			const round_violation_facility = this.round_violation_facility
			const layer_cutting_procedure = this.layer_cutting_procedure
			const layer_cutting_procedure_price = this.layer_cutting_procedure_price
			let checkArea = false
			let checkTypeArea = false
			this.asset_appropriate_area_arr.forEach(itemLandAsset => {
				if (itemLandAsset.total_area < 0) {
					checkTypeArea = true
				}
			})
			this.form.asset_general.forEach(item => {
				if (this.arrayTotalAppropriateArea[item.id] <= 0) {
					checkArea = true
				}
			})
			// check price
			let checkPrice = false
			this.form.asset_unit_price.forEach(item_price => {
				if (item_price.update_value < 0) {
					checkPrice = true
				}
			})
			let remaining_price = {
				remaining_commerce_price: this.remaining_commerce_price,
				land_type: this.type_is_not_facility
			}
			if (this.form.properties[0].property_detail && this.form.properties[0].property_detail.length < 2) {
				remaining_price = {}
			}
			if (typeof this.appraises !== 'undefined') {
				this.appraises.comparison_factor.forEach(comparison => {
					comparison.comparison_factor.forEach(data => {
						dataSave.push(data)
					})
				})
			}
			const payloadData = {
				asset_unit_price: asset_unit_price,
				asset_unit_area: asset_unit_area,
				appraise_adapter: appraise_adapter,
				comparison_factor: dataSave,
				other_comparison: otherDataSave,
				delete_other_comparison: dataDelete,
				round_total: +round_total,
				round_composite: +round_composite,
				round_violation_composite: +round_violation_composite,
				round_violation_facility: +round_violation_facility,
				layer_cutting_procedure: layer_cutting_procedure,
				layer_cutting_price: +layer_cutting_procedure_price,
				remaining_price: remaining_price
			}
			if (layer_cutting_procedure && !layer_cutting_procedure_price) {
				this.$toast.open({
					message: 'vui lòng nhập giá trị phương pháp cắt lớp',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_total < -7 || round_total > 7) {
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_composite < -7 || round_composite > 7) {
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_violation_composite < -7 || round_violation_composite > 7) {
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_violation_facility < -7 || round_violation_facility > 7) {
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkArea) {
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch phải lớn hơn 0',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkTypeArea) {
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkPrice) {
				this.$toast.open({
					message: 'Đơn giá UBND không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (Math.abs(this.mgcl1) > 15 || Math.abs(this.mgcl2) > 15 || Math.abs(this.mgcl3) > 15) {
				this.showWarningSave = true
				this.dataTab2 = payloadData
			} else this.handleSaveAdjustPlan(payloadData)
		},
		handleSaveTab2Ver2 () {
			// check data tab 1

			for (let i of this.form.asset_unit_price) {
				if (i.update_value < 0) {
					this.openMessage('Đơn giá UBND không được âm')
					return
				}
			}
			for (let i of this.form.asset_general) {
				if (this.arrayTotalAppropriateArea[i.id] <= 0) {
					this.openMessage('Diện tích phù hợp quy hoạch phải lớn hơn 0')
					return
				}
			}
			for (let i of this.asset_appropriate_area_arr) {
				if (i.total_area < 0) {
					this.openMessage('Diện tích phù hợp quy hoạch không được âm')
					return
				}
			}
			// check data tab 2
			if (this.mainPrice.isError) {
				this.openMessage('Đơn giá cắt lớp phải lớn hơn 0')
				return
			}
			if (this.mainPrice.price < 0) {
				this.openMessage('Đơn giá đất phải lớn hơn 0')
				return
			}
			const roundMessage = 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại'
			if (this.mainPrice.round > 7 || this.mainPrice.round < -7) {
				this.openMessage(roundMessage)
				return
			}
			if (this.purposePrice.length > 0) {
				for (let i of this.purposePrice) {
					if (i.round > 7 || i.round < -7) {
						this.openMessage(roundMessage)
						return
					}
					if (i.price < 0) {
						this.openMessage('Đơn giá đất phải lớn hơn 0')
						return
					}
				}
			}
			if (this.violatePrice.length > 0) {
				for (let i of this.violatePrice) {
					if (i.round > 7 || i.round < -7) {
						this.openMessage(roundMessage)
						return
					}
				}
			}
			const appraise_adapter = this.form.appraise_adapter
			for (let i of appraise_adapter) {
				if (i.change_violate_price < 0) {
					this.openMessage('Giá trị vi phạm quy hoạch không được âm')
					return
				}
			}
			const dataSave = []
			const asset_unit_price = this.form.asset_unit_price
			const asset_unit_area = this.form.asset_unit_area
			const otherDataSave = this.other_comparison
			const dataDelete = this.delete_other_comparison
			const land_asset_price = this.calTotalLandPrice
			if (typeof this.appraises !== 'undefined') {
				this.appraises.comparison_factor.forEach(comparison => {
					comparison.comparison_factor.forEach(data => {
						dataSave.push(data)
					})
				})
			}
			const payloadData = {
				asset_unit_price: asset_unit_price,
				asset_unit_area: asset_unit_area,
				appraise_adapter: appraise_adapter,
				comparison_factor: dataSave,
				other_comparison: otherDataSave,
				delete_other_comparison: dataDelete,
				main_price: this.mainPrice,
				purpose_price: this.purposePrice,
				violate_price: this.violatePrice,
				land_asset_price: land_asset_price
			}
			if (Math.abs(this.mgcl1) > 15 || Math.abs(this.mgcl2) > 15 || Math.abs(this.mgcl3) > 15) {
				this.dataTab2 = payloadData
				this.showWarningSave = true
			} else {
				this.handleSaveAdjustPlan(payloadData)
			}
		},
		openMessage (message, type = 'error', position = 'top-right', duration = 3000) {
			this.$toast.open({
				message: message,
				type: type,
				position: position,
				duration: duration
			})
		},
		handleSaveContinue () {
			this.handleSaveAdjustPlan(this.dataTab2)
		},
		async handleSaveAdjustPlan (payloadData) {
			this.isSubmit = true
			const res = await CertificateAsset.submitStep7(payloadData, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu bảng điều chỉnh QSDĐ thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.form.price_land_asset = res.data.price_land_asset
				this.form.price_other_asset = res.data.price_other_asset
				this.form.price_tangible_asset = res.data.price_tangible_asset
				this.form.price_total_asset = res.data.price_total_asset
				this.form.round_appraise_total = res.data.round_appraise_total
				let comparison_factor_resfresh = []
				await this.form.asset_general.forEach(asset_general => {
					const comparison_factor_TSSS = res.data.comparison_factor.filter(comparison => comparison.asset_general_id === asset_general.id)
					comparison_factor_resfresh.push({
						id: asset_general.id,
						comparison_factor: comparison_factor_TSSS
					})
				})
				this.delete_other_comparison = []
				this.other_comparison = []
				this.appraises.comparison_factor = comparison_factor_resfresh
				this.form.comparison_factor = res.data.comparison_factor
				this.getOtherComparison()
				// await this.refreshData()
				// this.$emit('updateDataStep7')
				this.key_render_5 += 1
				this.isSubmit = false
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		async refreshData () {
			const getDataStep7 = await CertificateAsset.getDataStep7(this.idData)
			if (getDataStep7.data) {
				this.form = getDataStep7.data
			}
		},
		// ------------------------------------------------- TAB_3 ---------------------------------------------------------------
		updateTotalOtherAsset (data) {
			this.form.price_other_asset = data.price_other_asset
			this.form.price_land_asset = data.price_land_asset
			this.form.price_tangible_asset = data.price_tangible_asset
			this.form.price_total_asset = data.price_total_asset
			this.form.round_appraise_total = data.round_appraise_total
			this.key_render_5 += 1
		},
		updateAssetPrice (data) {
			this.form.price_other_asset = data.price_other_asset
			this.form.price_land_asset = data.price_land_asset
			this.form.price_tangible_asset = data.price_tangible_asset
			this.form.price_total_asset = data.price_total_asset
			this.form.round_appraise_total = data.round_appraise_total
			this.key_render_5 += 1
		},
		// ------------------------------------------------- function calculation ---------------------------------------------------------------
		calculationViolationPrice (asset, indexAdapter) {
			// detail asset
			let asset1 = (typeof asset.asset_general[0] !== 'undefined') ? asset.asset_general[0] : null
			let asset2 = (typeof asset.asset_general[1] !== 'undefined') ? asset.asset_general[1] : null
			let asset3 = (typeof asset.asset_general[2] !== 'undefined') ? asset.asset_general[2] : null

			// get properties of asset
			asset.appraise_has_assets.forEach((appraiseHasAssets, index) => {
				if (appraiseHasAssets.asset_general_id === asset1.id) {
					asset1.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail1 = property
						}
					})
				}
				if (appraiseHasAssets.asset_general_id === asset2.id) {
					asset2.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail2 = property
						}
					})
				}
				if (appraiseHasAssets.asset_general_id === asset3.id) {
					asset3.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail3 = property
						}
					})
				}
			})
			// --------------------------- Tính đơn giá diện tích vi phạm quy hoạch ---------------------------//
			// đơn giá diện tích vi phạm quy hoạch TTSS-1
			let totalPriceViolationArea1 = 0
			let price_violation1 = []
			this.detail1.property_detail.forEach((item, index) => {
				let filterArea1 = this.form.asset_unit_area.filter(item_unit_area => item_unit_area.asset_general_id === this.detail1.asset_general_id)
				let filterPrice1 = this.form.asset_unit_price.filter(item_unit_price => item_unit_price.asset_general_id === this.detail1.asset_general_id)
				if (filterArea1 && filterArea1.length > 0) {
					filterArea1.forEach(item_area_filter1 => {
						// đơn giá UBND * diện tích VPQH
						filterPrice1.forEach(item_price_filter1 => {
							if (item_price_filter1.land_type_id === item_area_filter1.land_type_id) {
								price_violation1.push(+item_price_filter1.update_value * +item_area_filter1.violation_asset_area)
							}
						})
					})
				}
				if (price_violation1.length > 0 && price_violation1.length > 1) {
					totalPriceViolationArea1 = price_violation1.reduce((a, b) => a + b, 0)
					price_violation1 = []
				} else if (price_violation1.length > 0 && price_violation1.length === 1) {
					totalPriceViolationArea1 = price_violation1[0]
					price_violation1 = []
				}
			})
			// đơn giá diện tích vi phạm quy hoạch TTSS-2
			let totalPriceViolationArea2 = 0
			let price_violation2 = []
			this.detail2.property_detail.forEach((item, index) => {
				let filterArea2 = this.form.asset_unit_area.filter(item_unit_area => item_unit_area.asset_general_id === this.detail2.asset_general_id)
				let filterPrice2 = this.form.asset_unit_price.filter(item_unit_price => item_unit_price.asset_general_id === this.detail2.asset_general_id)
				if (filterArea2 && filterArea2.length > 0) {
					filterArea2.forEach(item_area_filter2 => {
						// đơn giá UBND * diện tích VPQH
						filterPrice2.forEach(item_price_filter2 => {
							if (item_price_filter2.land_type_id === item_area_filter2.land_type_id) {
								price_violation2.push(+item_price_filter2.update_value * +item_area_filter2.violation_asset_area)
							}
						})
					})
				}
				if (price_violation2.length > 0 && price_violation2.length > 1) {
					totalPriceViolationArea2 = price_violation2.reduce((a, b) => a + b, 0)
					price_violation2 = []
				} else if (price_violation2.length > 0 && price_violation2.length === 1) {
					totalPriceViolationArea2 = price_violation2[0]
					price_violation2 = []
				}
			})
			// đơn giá diện tích vi phạm quy hoạch TTSS-3
			let totalPriceViolationArea3 = 0
			let price_violation3 = []
			this.detail3.property_detail.forEach((item, index) => {
				let filterArea3 = this.form.asset_unit_area.filter(item_unit_area => item_unit_area.asset_general_id === this.detail3.asset_general_id)
				let filterPrice3 = this.form.asset_unit_price.filter(item_unit_price => item_unit_price.asset_general_id === this.detail3.asset_general_id)
				if (filterArea3 && filterArea3.length > 0) {
					filterArea3.forEach(item_area_filter3 => {
						// đơn giá UBND * diện tích VPQH
						filterPrice3.forEach(item_price_filter3 => {
							if (item_price_filter3.land_type_id === item_area_filter3.land_type_id) {
								price_violation3.push(+item_price_filter3.update_value * +item_area_filter3.violation_asset_area)
							}
						})
					})
				}
				if (price_violation3.length > 0 && price_violation3.length > 1) {
					totalPriceViolationArea3 = price_violation3.reduce((a, b) => a + b, 0)
					price_violation3 = []
				} else if (price_violation3.length > 0 && price_violation3.length === 1) {
					totalPriceViolationArea3 = price_violation3[0]
					price_violation3 = []
				}
			})
			if (indexAdapter === 0) {
				this.form.appraise_adapter[0].change_violate_price = totalPriceViolationArea1
			} else if (indexAdapter === 1) {
				this.form.appraise_adapter[1].change_violate_price = totalPriceViolationArea2
			} else if (indexAdapter === 2) {
				this.form.appraise_adapter[2].change_violate_price = totalPriceViolationArea3
			}
		},
		calculationChangePrice (asset, indexAdapter) {
			// detail asset
			let asset1 = (typeof asset.asset_general[0] !== 'undefined') ? asset.asset_general[0] : null
			let asset2 = (typeof asset.asset_general[1] !== 'undefined') ? asset.asset_general[1] : null
			let asset3 = (typeof asset.asset_general[2] !== 'undefined') ? asset.asset_general[2] : null

			// declare purpose converting price
			let purpose_price1 = 0
			let purpose_price2 = 0
			let purpose_price3 = 0

			// get properties of asset
			asset.appraise_has_assets.forEach((appraiseHasAssets, index) => {
				if (appraiseHasAssets.asset_general_id === asset1.id) {
					asset1.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail1 = property
						}
					})
				}
				if (appraiseHasAssets.asset_general_id === asset2.id) {
					asset2.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail2 = property
						}
					})
				}
				if (appraiseHasAssets.asset_general_id === asset3.id) {
					asset3.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail3 = property
						}
					})
				}
			})
			// get price facility
			this.baseUnitPrice = 0 // unit price of commitee
			this.baseAcronym = ''
			if ((typeof asset.properties[0] !== 'undefined') && (typeof asset.properties[0].property_detail !== 'undefined') && (asset.properties[0].property_detail.length === 1)) {
				this.baseUnitPrice = asset.properties[0].property_detail[0].circular_unit_price
				this.baseAcronym = asset.properties[0].property_detail[0].land_type_purpose.acronym
			} else {
				asset.properties[0].property_detail.forEach((item, index) => {
					if (item.is_transfer_facility || !index) {
						this.baseUnitPrice = item.circular_unit_price
						this.baseAcronym = item.land_type_purpose.acronym
					}
				})
			}

			this.baseUnitPrice = parseFloat(this.baseUnitPrice)

			// tính chi phí chuyển đổi TSSS-1
			this.price1 = 0
			let price_facility_asset1 = 0
			this.detail1.property_detail.forEach((item, index) => {
				this.form.asset_unit_price.forEach((itemPrice1, index2) => {
					if (this.baseAcronym === itemPrice1.land_type_data.acronym && (itemPrice1.asset_general_id === this.detail1.asset_general_id)) {
						if (itemPrice1.update === 2) {
							price_facility_asset1 = itemPrice1.update_value
						} else {
							price_facility_asset1 = itemPrice1.original_value
						}
					}
				})
				if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
					let circular_unit_price = item.circular_unit_price
					let appropriate_area = item.total_area
					if (typeof (this.form.asset_unit_price) !== 'undefined') {
						this.form.asset_unit_price.forEach((itemPrice1, index2) => {
							if ((itemPrice1.asset_general_id === this.detail1.asset_general_id) && (itemPrice1.land_type_id === item.land_type_purpose) && (itemPrice1.position_type_id === item.position_type_id)) {
								if (itemPrice1.update === 2) {
									circular_unit_price = itemPrice1.update_value
								} else {
									circular_unit_price = itemPrice1.original_value
								}
							}
						})
					}
					if (typeof (this.form.asset_unit_area) !== 'undefined') {
						this.form.asset_unit_area.forEach(itemArea1 => {
							if ((itemArea1.asset_general_id === this.detail1.asset_general_id) && (itemArea1.land_type_id === item.land_type_purpose) && (itemArea1.position_type_id === item.position_type_id)) {
								appropriate_area = item.total_area - itemArea1.violation_asset_area
							}
						})
					}
					let unitPrice = price_facility_asset1 - parseFloat(circular_unit_price)
					// this.price1 += unitPrice * parseFloat(appropriate_area)
					purpose_price1 += unitPrice * parseFloat(appropriate_area)
				}
			})
			// tính chi phí chuyển đổi TSSS-2
			this.price2 = 0
			let price_facility_asset2 = 0
			this.detail2.property_detail.forEach((item, index) => {
				this.form.asset_unit_price.forEach((itemPrice2, index2) => {
					if (this.baseAcronym === itemPrice2.land_type_data.acronym && (itemPrice2.asset_general_id === this.detail2.asset_general_id)) {
						if (itemPrice2.update === 2) {
							price_facility_asset2 = itemPrice2.update_value
						} else {
							price_facility_asset2 = itemPrice2.original_value
						}
					}
				})
				if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
					let circular_unit_price = item.circular_unit_price
					let appropriate_area = item.total_area
					if (typeof (this.form.asset_unit_price) !== 'undefined') {
						this.form.asset_unit_price.forEach((itemPrice2, index2) => {
							if ((itemPrice2.asset_general_id === this.detail2.asset_general_id) && (itemPrice2.land_type_id === item.land_type_purpose) && (itemPrice2.position_type_id === item.position_type_id)) {
								if (itemPrice2.update === 2) {
									circular_unit_price = itemPrice2.update_value
								} else {
									circular_unit_price = itemPrice2.original_value
								}
							}
						})
					}
					if (typeof (this.form.asset_unit_area) !== 'undefined') {
						this.form.asset_unit_area.forEach(itemArea2 => {
							if ((itemArea2.asset_general_id === this.detail2.asset_general_id) && (itemArea2.land_type_id === item.land_type_purpose) && (itemArea2.position_type_id === item.position_type_id)) {
								appropriate_area = item.total_area - itemArea2.violation_asset_area
							}
						})
					}
					let unitPrice = price_facility_asset2 - parseFloat(circular_unit_price)
					// this.price2 += unitPrice * parseFloat(appropriate_area)
					purpose_price2 += unitPrice * parseFloat(appropriate_area)
				}
			})
			// tính chi phí chuyển đổi TSSS-3
			this.price3 = 0
			let price_facility_asset3 = 0
			this.detail3.property_detail.forEach((item, index) => {
				this.form.asset_unit_price.forEach((itemPrice3, index3) => {
					if ((this.baseAcronym === itemPrice3.land_type_data.acronym) && (itemPrice3.asset_general_id === this.detail3.asset_general_id)) {
						if (itemPrice3.update === 2) {
							price_facility_asset3 = itemPrice3.update_value
						} else {
							price_facility_asset3 = itemPrice3.original_value
						}
					}
				})
				if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
					let circular_unit_price = item.circular_unit_price
					let appropriate_area = item.total_area
					if (typeof (this.form.asset_unit_price) !== 'undefined') {
						this.form.asset_unit_price.forEach((itemPrice3, index3) => {
							if ((itemPrice3.asset_general_id === this.detail3.asset_general_id) && (itemPrice3.land_type_id === item.land_type_purpose) && (itemPrice3.position_type_id === item.position_type_id)) {
								if (itemPrice3.update === 2) {
									circular_unit_price = itemPrice3.update_value
								} else {
									circular_unit_price = itemPrice3.original_value
								}
							}
						})
					}
					if (typeof (this.form.asset_unit_area) !== 'undefined') {
						this.form.asset_unit_area.forEach(itemArea3 => {
							if ((itemArea3.asset_general_id === this.detail3.asset_general_id) && (itemArea3.land_type_id === item.land_type_purpose) && (itemArea3.position_type_id === item.position_type_id)) {
								appropriate_area = item.total_area - itemArea3.violation_asset_area
							}
						})
					}
					let unitPrice = price_facility_asset3 - parseFloat(circular_unit_price)
					// this.price3 += unitPrice * parseFloat(appropriate_area)
					purpose_price3 += unitPrice * parseFloat(appropriate_area)
				}
			})

			if (indexAdapter === 0) {
				this.form.appraise_adapter[0].change_purpose_price = purpose_price1
			} else if (indexAdapter === 1) {
				this.form.appraise_adapter[1].change_purpose_price = purpose_price2
			} else if (indexAdapter === 2) {
				this.form.appraise_adapter[2].change_purpose_price = purpose_price3
			}
			// this.form.appraise_adapter[0].change_purpose_price = purpose_price1
			// this.form.appraise_adapter[1].change_purpose_price = purpose_price2
			// this.form.appraise_adapter[2].change_purpose_price = purpose_price3
		},
		calculation (asset) {
			let arrayPriceTem = []
			// detail asset
			let asset1 = (typeof asset.asset_general[0] !== 'undefined') ? asset.asset_general[0] : null
			let asset2 = (typeof asset.asset_general[1] !== 'undefined') ? asset.asset_general[1] : null
			let asset3 = (typeof asset.asset_general[2] !== 'undefined') ? asset.asset_general[2] : null

			let asset_percent1 = (typeof asset.appraise_adapter[0] !== 'undefined') ? asset.appraise_adapter[0].percent : null
			let asset_percent2 = (typeof asset.appraise_adapter[1] !== 'undefined') ? asset.appraise_adapter[1].percent : null
			let asset_percent3 = (typeof asset.appraise_adapter[2] !== 'undefined') ? asset.appraise_adapter[2].percent : null

			// detail contruction
			this.dtsxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].total_construction_base : 0
			this.dtsxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].total_construction_base : 0
			this.dtsxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].total_construction_base : 0
			// get properties of asset
			asset.appraise_has_assets.forEach((appraiseHasAssets, index) => {
				if (appraiseHasAssets.asset_general_id === asset1.id) {
					asset1.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail1 = property
						}
					})
				}
				if (appraiseHasAssets.asset_general_id === asset2.id) {
					asset2.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail2 = property
						}
					})
				}
				if (appraiseHasAssets.asset_general_id === asset3.id) {
					asset3.properties.forEach((property, index) => {
						if (property.id === appraiseHasAssets.asset_property_detail_id) {
							this.detail3 = property
						}
					})
				}
			})
			// --------------------------------Calculation violation area -----------------------------------------//
			// Sum violation area
			let arrayCalculation = []
			let total_vio_asset = null
			this.form.asset_general.forEach(assetItem => {
				let violationArray = this.form.asset_unit_area.filter(item => item.asset_general_id === assetItem.id)
				if (violationArray && violationArray.length > 0) {
					violationArray.forEach(item_violation_land => {
						arrayCalculation.push(item_violation_land.violation_asset_area)
					})
					total_vio_asset = arrayCalculation.reduce((a, b) => +a + +b, 0)
					this.arrayTotalViolationArea[assetItem.id] = total_vio_asset
					arrayCalculation = []
				}
			})
			// clear data memory
			this.asset_appropriate_area_arr = []
			// scan and  app
			this.form.asset_general.forEach(asset => {
				asset.properties.forEach(property_asset => {
					let appraiseHasAsset = this.form.appraise_has_assets.filter(item => item.asset_property_detail_id === property_asset.id)
					if (appraiseHasAsset && appraiseHasAsset.length > 0) {
						this.arrayTotalAppropriateArea[asset.id] = parseFloat((property_asset.asset_general_land_sum_area - this.arrayTotalViolationArea[asset.id]).toFixed(2))
						property_asset.property_detail.forEach(property_asset_detail => {
							let getDataUnitPrice = this.form.asset_unit_area.filter(itemVioLand => itemVioLand.asset_general_id === property_asset.asset_general_id && itemVioLand.land_type_id === property_asset_detail.land_type_purpose)
							if (getDataUnitPrice && getDataUnitPrice.length > 0) {
								this.asset_appropriate_area_arr.push({
									asset_general_id: property_asset.asset_general_id,
									total_area: parseFloat((property_asset_detail.total_area - +getDataUnitPrice[0].violation_asset_area).toFixed(2)),
									name_purpose_land_asset: property_asset_detail.land_type_purpose_data.acronym
								})
							}
						})
					}
				})
			})
			const mapViolation = new Map()
			this.asset_appropriate_area_arr.forEach(itemLandAsset => {
				if (!mapViolation.has(itemLandAsset.name_purpose_land_asset)) {
					mapViolation.set(itemLandAsset.name_purpose_land_asset)
					let dataLand = this.asset_appropriate_area_arr.filter(item => item.name_purpose_land_asset === itemLandAsset.name_purpose_land_asset)
					// check is exist about
					let checkExist = this.appropriate_zoning_land.filter(checkItem => checkItem.name_purpose_land === itemLandAsset.name_purpose_land_asset)
					if (checkExist && checkExist.length > 0) {
						// add unit if it is ex
						this.appropriate_zoning_land.forEach((appropriateLand, index) => {
							if (appropriateLand.name_purpose_land === itemLandAsset.name_purpose_land_asset) {
								this.appropriate_zoning_land[index]['asset_general_land'] = dataLand
							}
						})
					} else {
						// add if it not ex
						this.appropriate_zoning_land.push({
							name_purpose_land: itemLandAsset.name_purpose_land_asset,
							appropriate_appraise_land: 0,
							asset_general_land: dataLand
						})
					}
				}
			})

			// contruction price
			this.dgxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].unit_price_m2 : 0
			this.dgxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].unit_price_m2 : 0
			this.dgxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].unit_price_m2 : 0

			// remaining percent
			this.clcl1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].remaining_quality : 0
			this.clcl2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].remaining_quality : 0
			this.clcl3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].remaining_quality : 0

			// get price facility
			this.baseUnitPrice = 0 // unit price of commitee
			this.baseAcronym = ''
			if ((typeof asset.properties[0] !== 'undefined') && (typeof asset.properties[0].property_detail !== 'undefined') && (asset.properties[0].property_detail.length === 1)) {
				this.baseUnitPrice = asset.properties[0].property_detail[0].circular_unit_price
				this.baseAcronym = asset.properties[0].property_detail[0].land_type_purpose.acronym
			} else {
				asset.properties[0].property_detail.forEach((item, index) => {
					if (item.is_transfer_facility || !index) {
						this.baseUnitPrice = item.circular_unit_price
						this.baseAcronym = item.land_type_purpose.acronym
					}
				})
			}
			this.baseUnitPrice = parseFloat(this.baseUnitPrice)

			// declare violation price
			let totalPriceViolationArea1 = (typeof asset.appraise_adapter[0] !== 'undefined') ? +asset.appraise_adapter[0].change_violate_price : null
			let totalPriceViolationArea2 = (typeof asset.appraise_adapter[0] !== 'undefined') ? +asset.appraise_adapter[1].change_violate_price : null
			let totalPriceViolationArea3 = (typeof asset.appraise_adapter[0] !== 'undefined') ? +asset.appraise_adapter[2].change_violate_price : null

			// declare purpose converting price
			let change_purpose_price1 = (typeof asset.appraise_adapter[0] !== 'undefined') ? +asset.appraise_adapter[0].change_purpose_price : null
			let change_purpose_price2 = (typeof asset.appraise_adapter[1] !== 'undefined') ? +asset.appraise_adapter[1].change_purpose_price : null
			let change_purpose_price3 = (typeof asset.appraise_adapter[2] !== 'undefined') ? +asset.appraise_adapter[2].change_purpose_price : null
			// tính Tổng giá trị tài sản ước tính
			this.totalPriceEstimate1 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent1 * asset1.total_amount) / 100 : 0)
			this.totalPriceEstimate2 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent2 * asset2.total_amount) / 100 : 0)
			this.totalPriceEstimate3 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent3 * asset3.total_amount) / 100 : 0)

			// tính giá trị Quyền sử dụng đất của TSSS
			// ------------------------------------------------------------------------- Tổng giá trị ước tính - Đơn giá vi phạm quy hoạch + Chi phí chuyển mục đích sử dụng - Đơn giá đơn vị xây dựng
			this.totalPrice1 = ((typeof asset1.total_estimate_amount !== 'undefined') ? (asset_percent1 * asset1.total_amount) / 100 : 0) - totalPriceViolationArea1 + +change_purpose_price1 - (this.dgxd1 * this.dtsxd1 * this.clcl1 / 100)
			this.totalPrice2 = ((typeof asset2.total_estimate_amount !== 'undefined') ? (asset_percent2 * asset2.total_amount) / 100 : 0) - totalPriceViolationArea2 + +change_purpose_price2 - (this.dgxd2 * this.dtsxd2 * this.clcl2 / 100)
			this.totalPrice3 = ((typeof asset3.total_estimate_amount !== 'undefined') ? (asset_percent3 * asset3.total_amount) / 100 : 0) - totalPriceViolationArea3 + +change_purpose_price3 - (this.dgxd3 * this.dtsxd3 * this.clcl3 / 100)
			// tính đơn giá dất của TSSS
			this.dgd1 = (typeof this.detail1.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice1 / this.arrayTotalAppropriateArea[this.detail1.asset_general_id]) : 0
			this.dgd2 = (typeof this.detail2.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice2 / this.arrayTotalAppropriateArea[this.detail2.asset_general_id]) : 0
			this.dgd3 = (typeof this.detail3.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice3 / this.arrayTotalAppropriateArea[this.detail3.asset_general_id]) : 0

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
				let percentPl1 = +comparisonFactor1['phap_ly'].adjust_percent || 0
				let percentPl2 = +comparisonFactor2['phap_ly'].adjust_percent || 0
				let percentPl3 = +comparisonFactor3['phap_ly'].adjust_percent || 0
				// mức điều chỉnh của yếu tố PHÁP LÝ
				this.pricePl1 = percentPl1 * this.dgd1 / 100
				this.pricePl2 = percentPl2 * this.dgd2 / 100
				this.pricePl3 = percentPl3 * this.dgd3 / 100

				this.totalPricePL1 = this.dgd1 * (1 + percentPl1 / 100) // giá sau điều chỉnh
				this.totalPricePL2 = this.dgd2 * (1 + percentPl2 / 100) // giá sau điều chỉnh
				this.totalPricePL3 = this.dgd3 * (1 + percentPl3 / 100) // giá sau điều chỉnh

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
			this.priceQm1 = 0
			this.priceQm2 = 0
			this.priceQm3 = 0
			if ((typeof comparisonFactor1['quy_mo'] !== 'undefined') && comparisonFactor1['quy_mo'].status === 1) {
				let percentQm1 = +comparisonFactor1['quy_mo'].adjust_percent || 0
				let percentQm2 = +comparisonFactor2['quy_mo'].adjust_percent || 0
				let percentQm3 = +comparisonFactor3['quy_mo'].adjust_percent || 0

				// mức điều chỉnh của yếu tố QUY MÔ
				let price1 = this.roundPrice(percentQm1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentQm2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentQm3 * this.totalPricePL3 / 100, 0)
				this.priceQm1 = price1
				this.priceQm2 = price2
				this.priceQm3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['quy_mo'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentQm1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentQm2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentQm3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceCrmt1 = 0
			this.priceCrmt2 = 0
			this.priceCrmt3 = 0
			if ((typeof comparisonFactor1['chieu_rong_mat_tien'] !== 'undefined') && comparisonFactor1['chieu_rong_mat_tien'].status === 1) {
				let percentCrmt1 = +comparisonFactor1['chieu_rong_mat_tien'].adjust_percent || 0
				let percentCrmt2 = +comparisonFactor2['chieu_rong_mat_tien'].adjust_percent || 0
				let percentCrmt3 = +comparisonFactor3['chieu_rong_mat_tien'].adjust_percent || 0

				// mức điều chỉnh của yếu tố CHIỀU RỘNG MẶT TIỀN
				let price1 = this.roundPrice(percentCrmt1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentCrmt2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentCrmt3 * this.totalPricePL3 / 100, 0)
				this.priceCrmt1 = price1
				this.priceCrmt2 = price2
				this.priceCrmt3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['chieu_rong_mat_tien'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentCrmt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentCrmt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentCrmt3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceCskd1 = 0
			this.priceCskd2 = 0
			this.priceCskd3 = 0
			if ((typeof comparisonFactor1['chieu_sau_khu_dat'] !== 'undefined') && comparisonFactor1['chieu_sau_khu_dat'].status === 1) {
				let percentCskd1 = +comparisonFactor1['chieu_sau_khu_dat'].adjust_percent || 0
				let percentCskd2 = +comparisonFactor2['chieu_sau_khu_dat'].adjust_percent || 0
				let percentCskd3 = +comparisonFactor3['chieu_sau_khu_dat'].adjust_percent || 0

				// mức điều chỉnh của yếu tố CHIỀU SÂU KHU ĐẤT
				let price1 = this.roundPrice(percentCskd1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentCskd2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentCskd3 * this.totalPricePL3 / 100, 0)
				this.priceCskd1 = price1
				this.priceCskd2 = price2
				this.priceCskd3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['chieu_sau_khu_dat'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentCskd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentCskd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentCskd3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceHdd1 = 0
			this.priceHdd2 = 0
			this.priceHdd3 = 0
			if ((typeof comparisonFactor1['hinh_dang_dat'] !== 'undefined') && comparisonFactor1['hinh_dang_dat'].status === 1) {
				let percentHdd1 = +comparisonFactor1['hinh_dang_dat'].adjust_percent || 0
				let percentHdd2 = +comparisonFactor2['hinh_dang_dat'].adjust_percent || 0
				let percentHdd3 = +comparisonFactor3['hinh_dang_dat'].adjust_percent || 0
				// mức điều chỉnh của yếu tố HÌNH DÁNG DẤT
				let price1 = this.roundPrice(percentHdd1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentHdd2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentHdd3 * this.totalPricePL3 / 100, 0)
				this.priceHdd1 = price1
				this.priceHdd2 = price2
				this.priceHdd3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['hinh_dang_dat'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentHdd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentHdd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentHdd3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceKcd1 = 0
			this.priceKcd2 = 0
			this.priceKcd3 = 0
			if ((typeof comparisonFactor1['ket_cau_duong'] !== 'undefined') && comparisonFactor1['ket_cau_duong'].status === 1) {
				let percentKcd1 = +comparisonFactor1['ket_cau_duong'].adjust_percent || 0
				let percentKcd2 = +comparisonFactor2['ket_cau_duong'].adjust_percent || 0
				let percentKcd3 = +comparisonFactor3['ket_cau_duong'].adjust_percent || 0
				// mức điều chỉnh của yếu tố KẾT CẤU ĐƯỜNG
				let price1 = this.roundPrice(percentKcd1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentKcd2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentKcd3 * this.totalPricePL3 / 100, 0)
				this.priceKcd1 = price1
				this.priceKcd2 = price2
				this.priceKcd3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['ket_cau_duong'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentKcd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentKcd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentKcd3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceDrd1 = 0
			this.priceDrd2 = 0
			this.priceDrd3 = 0
			if ((typeof comparisonFactor1['do_rong_duong'] !== 'undefined') && comparisonFactor1['do_rong_duong'].status === 1) {
				let percentDrd1 = +comparisonFactor1['do_rong_duong'].adjust_percent || 0
				let percentDrd2 = +comparisonFactor2['do_rong_duong'].adjust_percent || 0
				let percentDrd3 = +comparisonFactor3['do_rong_duong'].adjust_percent || 0

				// mức điều chỉnh của yếu tố ĐỘ RỘNG ĐƯỜNG
				let price1 = this.roundPrice(percentDrd1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentDrd2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentDrd3 * this.totalPricePL3 / 100, 0)
				this.priceDrd1 = price1
				this.priceDrd2 = price2
				this.priceDrd3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['do_rong_duong'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentDrd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDrd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDrd3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceDkht1 = 0
			this.priceDkht2 = 0
			this.priceDkht3 = 0
			if ((typeof comparisonFactor1['dieu_kien_ha_tang'] !== 'undefined') && comparisonFactor1['dieu_kien_ha_tang'].status === 1) {
				let percentDkht1 = +comparisonFactor1['dieu_kien_ha_tang'].adjust_percent || 0
				let percentDkht2 = +comparisonFactor2['dieu_kien_ha_tang'].adjust_percent || 0
				let percentDkht3 = +comparisonFactor3['dieu_kien_ha_tang'].adjust_percent || 0
				// mức điều chỉnh của yếu tố ĐIỀU KIỆN HẠ TẦNG
				let price1 = this.roundPrice(percentDkht1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentDkht2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentDkht3 * this.totalPricePL3 / 100, 0)
				this.priceDkht1 = price1
				this.priceDkht2 = price2
				this.priceDkht3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['dieu_kien_ha_tang'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentDkht1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDkht2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDkht3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceKd1 = 0
			this.priceKd2 = 0
			this.priceKd3 = 0
			if ((typeof comparisonFactor1['kinh_doanh'] !== 'undefined') && comparisonFactor1['kinh_doanh'].status === 1) {
				let percentKd1 = +comparisonFactor1['kinh_doanh'].adjust_percent || 0
				let percentKd2 = +comparisonFactor2['kinh_doanh'].adjust_percent || 0
				let percentKd3 = +comparisonFactor3['kinh_doanh'].adjust_percent || 0
				// mức điều chỉnh của yếu tố KINH DOANH
				let price1 = this.roundPrice(percentKd1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentKd2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentKd3 * this.totalPricePL3 / 100, 0)
				this.priceKd1 = price1
				this.priceKd2 = price2
				this.priceKd3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['kinh_doanh'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentKd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentKd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentKd3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceAnmts1 = 0
			this.priceAnmts2 = 0
			this.priceAnmts3 = 0
			if ((typeof comparisonFactor1['an_ninh_moi_truong_song'] !== 'undefined') && comparisonFactor1['an_ninh_moi_truong_song'].status === 1) {
				let percentAnmts1 = +comparisonFactor1['an_ninh_moi_truong_song'].adjust_percent || 0
				let percentAnmts2 = +comparisonFactor2['an_ninh_moi_truong_song'].adjust_percent || 0
				let percentAnmts3 = +comparisonFactor3['an_ninh_moi_truong_song'].adjust_percent || 0
				// mức điều chỉnh của yếu tố AN NINH, MÔI TRƯỜNG SỐNG
				let price1 = this.roundPrice(percentAnmts1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentAnmts2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentAnmts3 * this.totalPricePL3 / 100, 0)
				this.priceAnmts1 = price1
				this.priceAnmts2 = price2
				this.priceAnmts3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['an_ninh_moi_truong_song'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentAnmts1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentAnmts2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentAnmts3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.pricePt1 = 0
			this.pricePt2 = 0
			this.pricePt3 = 0
			if ((typeof comparisonFactor1['phong_thuy'] !== 'undefined') && comparisonFactor1['phong_thuy'].status === 1) {
				let percentPt1 = +comparisonFactor1['phong_thuy'].adjust_percent || 0
				let percentPt2 = +comparisonFactor2['phong_thuy'].adjust_percent || 0
				let percentPt3 = +comparisonFactor3['phong_thuy'].adjust_percent || 0
				// mức điều chỉnh của yếu tố PHONG THỦY
				let price1 = this.roundPrice(percentPt1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentPt2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentPt3 * this.totalPricePL3 / 100, 0)
				this.pricePt1 = price1
				this.pricePt2 = price2
				this.pricePt3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['phong_thuy'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentPt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentPt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentPt3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceQh1 = 0
			this.priceQh2 = 0
			this.priceQh3 = 0
			if ((typeof comparisonFactor1['quy_hoach'] !== 'undefined') && comparisonFactor1['quy_hoach'].status === 1) {
				let percentQh1 = +comparisonFactor1['quy_hoach'].adjust_percent || 0
				let percentQh2 = +comparisonFactor2['quy_hoach'].adjust_percent || 0
				let percentQh3 = +comparisonFactor3['quy_hoach'].adjust_percent || 0
				// mức điều chỉnh của yếu tố AN NINH, MÔI TRƯỜNG SỐNG
				let price1 = this.roundPrice(percentQh1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentQh2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentQh3 * this.totalPricePL3 / 100, 0)
				this.priceQh1 = price1
				this.priceQh2 = price2
				this.priceQh3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['quy_hoach'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentQh1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentQh2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentQh3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceDktt1 = 0
			this.priceDktt2 = 0
			this.priceDktt3 = 0
			if ((typeof comparisonFactor1['dieu_kien_thanh_toan'] !== 'undefined') && comparisonFactor1['dieu_kien_thanh_toan'].status === 1) {
				let percentDktt1 = +comparisonFactor1['dieu_kien_thanh_toan'].adjust_percent || 0
				let percentDktt2 = +comparisonFactor2['dieu_kien_thanh_toan'].adjust_percent || 0
				let percentDktt3 = +comparisonFactor3['dieu_kien_thanh_toan'].adjust_percent || 0
				// mức điều chỉnh của yếu tố ĐIỀU KIỆN THANH TOÁN
				let price1 = this.roundPrice(percentDktt1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentDktt2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentDktt3 * this.totalPricePL3 / 100, 0)
				this.priceDktt1 = price1
				this.priceDktt3 = price2
				this.priceDktt3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['dieu_kien_thanh_toan'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentDktt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDktt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDktt3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			this.priceGt1 = 0
			this.priceGt2 = 0
			this.priceGt3 = 0
			if ((typeof comparisonFactor1['giao_thong'] !== 'undefined') && comparisonFactor1['giao_thong'].status === 1) {
				let percentGt1 = +comparisonFactor1['giao_thong'].adjust_percent || 0
				let percentGt2 = +comparisonFactor2['giao_thong'].adjust_percent || 0
				let percentGt3 = +comparisonFactor3['giao_thong'].adjust_percent || 0
				// mức điều chỉnh của yếu tố GIAO THÔNG
				let price1 = this.roundPrice(percentGt1 * this.totalPricePL1 / 100, 0)
				let price2 = this.roundPrice(percentGt2 * this.totalPricePL2 / 100, 0)
				let price3 = this.roundPrice(percentGt3 * this.totalPricePL3 / 100, 0)
				this.priceGt1 = price1
				this.priceGt2 = price2
				this.priceGt3 = price3
				adjustPrice1 += price1
				adjustPrice2 += price2
				adjustPrice3 += price3
				this.adjustPriceData['giao_thong'] = [adjustPrice1, adjustPrice2, adjustPrice3]

				this.comparisonFactorChange1 += (percentGt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentGt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentGt3 !== 0) ? 1 : 0
			}

			// These field must be set = 0 before check
			// Incase factor has been removed manualy it will not be updated if put these inside condition block
			// this.priceYtk1 = 0
			// this.priceYtk2 = 0
			// this.priceYtk3 = 0
			// if ((typeof comparisonFactor1['yeu_to_khac'] !== 'undefined') && comparisonFactor2['yeu_to_khac'] && comparisonFactor3['yeu_to_khac'] && comparisonFactor1['yeu_to_khac'].status === 1) {
			// 	percentYtk1 = +comparisonFactor1['yeu_to_khac'].adjust_percent || 0
			// 	percentYtk2 = +comparisonFactor2['yeu_to_khac'].adjust_percent || 0
			// 	percentYtk3 = +comparisonFactor3['yeu_to_khac'].adjust_percent || 0
			// 	// mức điều chỉnh của yếu tố YẾU TỐ KHÁC
			// 	this.priceYtk1 = this.roundPrice(percentYtk1 * this.totalPricePL1 / 100, 0)
			// 	this.priceYtk2 = this.roundPrice(percentYtk2 * this.totalPricePL2 / 100, 0)
			// 	this.priceYtk3 = this.roundPrice(percentYtk3 * this.totalPricePL3 / 100, 0)
			// 	adjustPrice1 += this.priceYtk1
			// 	adjustPrice2 += this.priceYtk2
			// 	adjustPrice3 += this.priceYtk3
			// 	this.adjustPriceData['yeu_to_khac'] = [adjustPrice1, adjustPrice2, adjustPrice3]

			// 	this.comparisonFactorChange1 += (percentYtk1 !== 0) ? 1 : 0
			// 	this.comparisonFactorChange2 += (percentYtk2 !== 0) ? 1 : 0
			// 	this.comparisonFactorChange3 += (percentYtk3 !== 0) ? 1 : 0
			// }

			// Yếu khác thêm động
			let arrayAdjustPrice = [adjustPrice1, adjustPrice2, adjustPrice3]
			let mgcd_price_other_abs = {}
			let mgcd_price_other = {}
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
							if (+dataOther.adjust_percent !== 0) {
								if (item.id === asset1.id) {
									this.comparisonFactorChange1 += 1
								} else if (item.id === asset2.id) {
									this.comparisonFactorChange2 += 1
								} else if (item.id === asset3.id) {
									this.comparisonFactorChange3 += 1
								}
							}
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
			// console.log(this.price_other_comparison)
			asset.asset_general.forEach((asset, index) => {
				const asset_comparison_data = this.other_comparison.filter(data => data.asset_general_id === asset.id)
				mgcd_price_other_abs[index] = 0
				mgcd_price_other[index] = 0
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
			this.tldcg1 = Math.abs(this.pricePl1) + Math.abs(this.priceHdd1) + Math.abs(this.priceKcd1) + Math.abs(this.priceKd1) + Math.abs(this.priceDkht1) + Math.abs(this.pricePt1) + Math.abs(this.priceDktt1) + Math.abs(this.priceQm1) + Math.abs(this.priceCrmt1) + Math.abs(this.priceCskd1) + Math.abs(this.priceDrd1) + Math.abs(mgcd_price_other_abs[0])
			this.tldcg2 = Math.abs(this.pricePl2) + Math.abs(this.priceHdd2) + Math.abs(this.priceKcd2) + Math.abs(this.priceKd2) + Math.abs(this.priceDkht2) + Math.abs(this.pricePt2) + Math.abs(this.priceDktt2) + Math.abs(this.priceQm2) + Math.abs(this.priceCrmt2) + Math.abs(this.priceCskd2) + Math.abs(this.priceDrd2) + Math.abs(mgcd_price_other_abs[1])
			this.tldcg3 = Math.abs(this.pricePl3) + Math.abs(this.priceHdd3) + Math.abs(this.priceKcd3) + Math.abs(this.priceKd3) + Math.abs(this.priceDkht3) + Math.abs(this.pricePt3) + Math.abs(this.priceDktt3) + Math.abs(this.priceQm3) + Math.abs(this.priceCrmt3) + Math.abs(this.priceCskd3) + Math.abs(this.priceDrd3) + Math.abs(mgcd_price_other_abs[2])

			// tổng giá trị điều chỉnh thuần
			this.tldc1 = this.pricePl1 + this.priceHdd1 + this.priceKcd1 + this.priceKd1 + this.priceDkht1 + this.pricePt1 + this.priceDktt1 + this.priceQm1 + this.priceCrmt1 + this.priceCskd1 + this.priceDrd1 + this.priceGt1 + this.priceAnmts1 + this.priceQh1 + mgcd_price_other[0]
			this.tldc2 = this.pricePl2 + this.priceHdd2 + this.priceKcd2 + this.priceKd2 + this.priceDkht2 + this.pricePt2 + this.priceDktt2 + this.priceQm2 + this.priceCrmt2 + this.priceCskd2 + this.priceDrd2 + this.priceGt2 + this.priceAnmts2 + this.priceQh2 + mgcd_price_other[1]
			this.tldc3 = this.pricePl3 + this.priceHdd3 + this.priceKcd3 + this.priceKd3 + this.priceDkht3 + this.pricePt3 + this.priceDktt3 + this.priceQm3 + this.priceCrmt3 + this.priceCskd3 + this.priceDrd3 + this.priceGt3 + this.priceAnmts3 + this.priceQh3 + mgcd_price_other[2]

			// tính mức giá chỉ dẫn của TSSS
			this.mgcd1 = this.dgd1 + this.tldc1
			this.mgcd2 = this.dgd2 + this.tldc2
			this.mgcd3 = this.dgd3 + this.tldc3

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
			this.mgtb = ((asset.asset_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.asset_general.length)) : 0
			let arrayMGTN = [this.mgcd1, this.mgcd2, this.mgcd3]
			if (this.form.unify_indicative_price_slug === 'thap-nhat') {
				this.mgtn = Math.min(...arrayMGTN)
				this.mgtnTemp = Math.min(...arrayMGTN)
			} else if (this.form.unify_indicative_price_slug === 'cao-nhat') {
				this.mgtn = Math.max(...arrayMGTN)
				this.mgtnTemp = Math.max(...arrayMGTN)
			} else if (this.form.unify_indicative_price_slug === 'trung-binh') {
				this.mgtn = ((asset.asset_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.asset_general.length)) : 0
				this.mgtnTemp = ((asset.asset_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.asset_general.length)) : 0
			}

			// check nếu là phương pháp cắt lớp
			if (this.layer_cutting_procedure === true) {
				this.round_layer_cutting_produre = this.layer_cutting_procedure_price
				this.mgtnTemp = this.layer_cutting_procedure_price
			} else {
				this.layer_cutting_procedure_price = parseFloat(this.mgtn).toFixed(0)
			}

			// tỉ lệ chênh lệch mức giá trung bình trên mức giá chỉ dẫn
			this.mgcl1 = this.mgtb ? Math.round((this.mgcd1 - this.mgtb) / this.mgtb * 100, 0) : 0
			this.mgcl2 = this.mgtb ? Math.round((this.mgcd2 - this.mgtb) / this.mgtb * 100, 0) : 0
			this.mgcl3 = this.mgtb ? Math.round((this.mgcd3 - this.mgtb) / this.mgtb * 100, 0) : 0

			// biên độ điều chỉnh
			asset.asset_general.forEach(assetData => {
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

			// --------------------------- tính đơn giá đất thị trường còn lại ------------------------------//
			// Phương pháp: chi phí chuyển đổi MĐSD đất
			if (this.form.composite_land_remaning_slug === 'theo-chi-phi-chuyen-mdsd-dat') {
				this.type_method = 'Phương pháp: chi phí chuyển đổi MĐSD đất'
				this.checkProcedure = true
				let array_difference_price = []
				let difference_price = null
				if (this.form.properties && this.form.properties.length > 0) {
					this.form.properties.forEach(property => {
						property.property_detail.forEach(propertyDetail => {
							let array_property_facility = property.property_detail.filter(item => item.is_transfer_facility)
							if (!propertyDetail.is_transfer_facility) {
								array_difference_price.push(array_property_facility[0].circular_unit_price - propertyDetail.circular_unit_price)
							}
						})
					})
				}
				if (array_difference_price && array_difference_price.length > 0) {
					difference_price = array_difference_price.reduce((a, b) => a + b, 0)
				}
				this.remaining_commerce_price = parseFloat(this.formatCurrent(this.mgtnTemp) - difference_price).toFixed(0)
				// Phương pháp: tỷ lệ % giá đất cơ sở chính
			} else if (this.form.composite_land_remaning_slug === 'theo-ty-le-gia-dat-co-so-chinh') {
				this.type_method = `Phương pháp: tỷ lệ ${this.form.composite_land_remaning_value}% giá đất cơ sở chính`
				this.checkProcedure = true
				this.remaining_commerce_price = parseFloat(this.formatCurrent(this.mgtnTemp) * this.form.composite_land_remaning_value / 100).toFixed(0)
				// Phương pháp: tính giá độc lập
			} else if (this.form.composite_land_remaning_slug === 'theo-phuong-phap-doc-lap') {
				this.type_method = 'Phương pháp: tính giá độc lập'
				this.checkProcedure = false
			}
			// ------------------------------  ---tính gía đất vi pham quy hoạch-------------------------------- //
			if (this.form.properties[0].property_detail.find(property_detail => property_detail.is_zoning === true)) {
				this.show_violation_price = true
			} else this.show_violation_price = false
			// Phương pháp: giá đất QĐ UBND
			if (this.form.planning_violation_price_slug === 'theo-gia-dat-qd-ubnd' && this.form.properties && this.form.properties.length > 0) {
				let facility_price = 0
				let composite_price = 0
				this.type_violation_method = 'Phương pháp: giá đất QĐ UBND'
				if (this.form.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true)) {
					facility_price = this.form.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).circular_unit_price
				}
				if (this.form.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false)) {
					composite_price = this.form.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).circular_unit_price
				}
				this.violation_facility_price = facility_price
				this.violation_composite_price = composite_price
				// Phương pháp: tỷ lệ % giá đất thị trường
			} else if (this.form.planning_violation_price_slug === 'theo-ty-le-gia-dat-thi-truong' && this.form.properties && this.form.properties.length > 0) {
				this.type_violation_method = `Phương pháp: tỷ lệ ${this.form.planning_violation_price_value}% giá đất thị trường`
				this.violation_facility_price = this.layer_cutting_procedure ? this.formatCurrent(this.round_layer_cutting_produre) * +this.form.planning_violation_price_value / 100 : this.formatCurrent(this.mgtn) * this.form.planning_violation_price_value / 100
				this.violation_composite_price = this.formatRoundComposite(this.remaining_commerce_price) * this.form.planning_violation_price_value / 100
			}
			this.key_render_around += 1
		},
		handlePrint () {
			this.printEstimateAssetPrice()
			this.openPrint = true
		},
		calculatePrintData (data) {
			// bảo test
			if (data.properties[0] && data.properties[0].property_detail && data.properties[0].property_detail.length > 0) {
				let propertyDetail = data.properties[0].property_detail
				var description = ''
				let area = 0
				let landTypeDescription = ''
				let price = 0
				let total = 0
				let totalArea = 0
				let totalAmount = 0
				const violateDescription = 'Đất vi phạm quy hoạch'
				const purposeDescription = 'Đất phù hợp quy hoạch'
				const assetPrice = data.asset_price
				let acronym = ''
				let purposePrice = []
				let violatePrice = []
				let dataTmp = []
				let roundData = []
				let round = 0
				propertyDetail.forEach(item => {
					acronym = item.land_type_purpose.acronym
					purposePrice = assetPrice.filter(i => i.slug === 'land_asset_purpose_' + acronym + '_price' && i.value > 0)
					violatePrice = assetPrice.filter(i => i.slug === 'land_asset_purpose_' + acronym + '_violation_price' && i.value > 0)
					if (purposePrice && purposePrice.length > 0) {
						if (item.main_area > 0) {
							if (item.is_transfer_facility) {
								roundData = assetPrice.filter(i => i.slug === 'round_total')
							} else {
								roundData = assetPrice.filter(i => i.slug === 'round_composite')
							}
							description = purposeDescription
							landTypeDescription = item.land_type_purpose.description
							area = item.main_area
							round = roundData[0] ? roundData[0].value : 0
							price = this.roundPrice(purposePrice[0].value, round)
							total = area * price
							totalArea = totalArea + area
							totalAmount = totalAmount + total
							dataTmp.push({
								description: description,
								area: area,
								price: price,
								landTypeDescription: landTypeDescription,
								total: total
							})
						}
					}
					if (violatePrice && violatePrice.length > 0) {
						if (item.planning_area > 0) {
							roundData = assetPrice.filter(i => i.slug === 'round_total')
							round = roundData[0] ? roundData[0].value : 0
							description = violateDescription
							landTypeDescription = item.land_type_purpose.description
							area = item.planning_area
							price = this.roundPrice(violatePrice[0].value, round)
							total = area * price
							totalArea = totalArea + area
							totalAmount = totalAmount + total
							dataTmp.push({
								description: description,
								area: area,
								price: price,
								landTypeDescription: landTypeDescription,
								total: total
							})
						}
					}
				})
				this.reportDataDetail = dataTmp
				this.reportDataTotal.total_area = totalArea
				this.reportDataTotal.total_price = totalAmount.toFixed(0)
				this.reportDataTotal.doc_num = ''
				this.reportDataTotal.person = ''
				if (totalAmount > 0) {

				}
			}
		},
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
		async printEstimateAssetPrice () {
			let id = this.idData ? this.idData : ''
			const res = await CertificateAsset.postEstimateAssetPrice(id)
			if (res.data) {
				this.reportData = res.data
			} else {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			}
		},
		getAssetPrice (data) {
			let landDetails = data.properties[0].property_detail
			const strSlug = 'land_asset_purpose_'
			let assetPrice = data.asset_price.filter(item => item.slug.substr(0, strSlug.length) === strSlug)
			let priceLand = []
			// let price = []
			if (landDetails.length > 0) {
				landDetails.forEach(item => {
					priceLand = assetPrice.filter(f => f.slug.substr(strSlug.length, 3) === item.land_type_purpose.acronym)
					// console.log(priceLand)
					if (priceLand && priceLand.length > 0) {
						priceLand.forEach(i => {
							// price.push({
							// 	acronym: item.land_type_purpose.acronym,
							// 	price_slug: i.slug
							// 	price_value: i.value

							// })
						})
					}
				})
			}
			// console.log(price)
			this.price = assetPrice
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

	overflow: auto;
  margin-top: 1rem;
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
  color: #3D4D65;
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
  color: #3D4D65;
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
    color: #3D4D65;
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
.document_action {
	cursor: pointer;
	background: #FFFFFF;
}
</style>
