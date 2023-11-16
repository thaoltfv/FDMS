<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer">
      <div class="modal-detail d-flex justify-content-center align-items-center">
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Chỉnh sửa phụ lục 1</h2>
            </div>
          </div>
          <div class="contain-detail" >
          <!---------------------------------------BẢNG CHỈNH SỬA QUY MÔ DIỆN TÍCH ĐẤT---------------------------------->
            <div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">THÔNG SỐ DIỆN TÍCH VÀ QUY HOẠCH</h3>
                <img class="img-dropdown" :class="!showAreaLand? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showAreaLand = !showAreaLand">
              </div>
            </div>
            <div class="card-body card-info" v-if="showAreaLand">
              <div class="container-table" v-if="appraises.asset_general && appraises.asset_general.length > 0">
                <table class="table_price_committee">
                  <thead>
                    <tr>
                      <th>Loại đất</th>
                      <th>TSTĐ</th>
                      <th v-for="(asset, index) in appraises.asset_general" :key="'header' + index" >{{ 'TSSS ' + asset.id }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><strong>Quy mô (Tổng diện tích) (m<sup>2</sup>)</strong></td>
                      <td><strong>{{appraises.properties && appraises.properties.length > 0 ? parseFloat(appraises.properties[0].appraise_land_sum_area).toFixed(2) : "-"}}</strong></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                        <div v-for="(property, indexProperty) in appraises.propertyChoosing" v-if="property.asset_general_id === asset.id" :key="'propertyArea' + indexProperty">
                          <strong>{{parseFloat(property.asset_general_land_sum_area).toFixed(2)}}</strong>
                        </div>
                      </td>
                    </tr>
                    <tr >
                      <td><strong>Đất phù hợp quy hoạch (m<sup>2</sup>)</strong></td>
                      <td><strong>{{parseFloat(total_appraise_area).toFixed(2)}}</strong></td>
                      <td v-for="(asset, indexAppropriateArea) in appraises.asset_general" :key="'viola' + indexAppropriateArea">
                        <strong :style="arrayTotalAppropriateArea[asset.id] <= 0 ? {color:'red'} : {}">{{parseFloat(arrayTotalAppropriateArea[asset.id]).toFixed(2)}}</strong>
                      </td>
                    </tr>

                    <tr v-for="(area, index) in appropriate_zoning_land" :key="'area-price' + index">
                      <td>{{area.name_purpose_land}}</td>
                      <td>{{parseFloat(area.appropriate_appraise_land).toFixed(2)}}</td>
                      <td v-for="(asset, index_area) in appraises.asset_general" :key="'area-land' + index_area">
                        <div v-if="!checkDataPriceUBND(asset, area.asset_general_land)">
                          <div v-for="(area_data, index_asset_land) in area.asset_general_land" :key="'areatype' + index_asset_land" >
                            <div :style="area_data.total_area < 0 ? {color:'red'} : {}" v-if="area_data.asset_general_id === asset.id">
                              {{validateArea(area_data.total_area)}}
                            </div>
                          </div>
                        </div>
                        <div v-else>-</div>
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Đất vi phạm quy hoạch (m<sup>2</sup>)</strong></td>
                      <td><strong>{{parseFloat(total_vio_appraise_area).toFixed(2)}}</strong></td>
                      <td v-for="(asset, indexVio) in appraises.asset_general" :key="'viola' + indexVio">
                        <strong>{{parseFloat(arrayTotalViolationArea[asset.id]).toFixed(2)}}</strong>
                      </td>
                    </tr>
                    <tr v-for="(violation_area, index) in violation_zoning_land" :key="'area-violation-price' + index">
                      <td>{{violation_area.name_purpose_land}}</td>
                      <td>{{parseFloat(violation_area.violation_appraise_land).toFixed(2)}}</td>
                      <td v-for="(asset, index_area) in appraises.asset_general" :key="'area-violation-land' + index_area">
                        <div v-if="!checkDataPriceUBND(asset, violation_area.asset_general_land)">
                          <div v-for="(area_data, index_violation_land) in violation_area.asset_general_land" :key="'areaViotype' + index_violation_land" >
                              <InputNumberFormat
                                v-if="area_data.asset_general_id === asset.id"
                                class="label-none input_number_center"
                                v-model="area_data.violation_asset_area"
                                vid="violation_asset_area"
                                label="area-violation"
                                :max="999999999"
                                :min="0"
                                :step="0.01"
                                @change="changeViolationLand($event, area_data)"
                              />
                          </div>
                        </div>
                        <div v-else>-</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!---------------------------------------BẢNG CHỈNH SỬA ĐƠN GIÁ UBND TSTĐG VÀ TSSS---------------------------------->
            <div class="marginTop card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">THÔNG SỐ ĐƠN GIÁ ĐẤT UBND</h3>
                <img class="img-dropdown" :class="!showPriceCommitee? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showPriceCommitee = !showPriceCommitee">
              </div>
            </div>
            <div class="card-body card-info" v-if="showPriceCommitee">
              <div class="container-table" v-if="appraises.asset_general && appraises.asset_general.length > 0">
                <table class="table_price_committee">
                  <thead>
                    <tr>
                      <th>Đơn giá theo QĐ</th>
                      <th>TSTĐ</th>
                      <th v-for="(asset, index) in appraises.asset_general" :key="'header' + index" >{{ 'TSSS ' + asset.id }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(data, index) in data_land_price_demo" :key="'body-price' + index" >
                      <td>{{data.purpose_land + (data.facility ? ' (Đất cơ sở)' : '')}}</td>
                      <td>
                        <div v-if="data.price_appraise">
                          {{format(data.price_appraise) + 'đ'}}
                        </div>
                        <div v-else>-</div>
                      </td>
                      <td v-for="(asset, index_price) in appraises.asset_general" :key="'assetType' + index_price">
                        <div v-if="!checkDataPriceUBND(asset, data.price_asset)">
                          <div v-for="(price_data, index) in data.price_asset" :key="'pricetype' + index" v-if="price_data.asset_general_id === asset.id">
                            <InputNumberFormat
                                :class="{'label-none input_number_center ': true, 'input_number_error': price_data.update_value < 0 }"
                                v-model="price_data.update_value"
                                vid="price_asset"
                                :max="999999999999999"
                                :min="-999999999999999"
                                :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                @change="changePriceLand($event, price_data)"
                              />
                          </div>
                        </div>
                        <div v-else>
                          <div>-</div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!---------------------------------------BẢNG THÔNG TIN TSTĐG VÀ TSSS---------------------------------->
            <div class="marginTop card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">BẢNG TỔNG HỢP THÔNG TIN TSTĐ VÀ TSSS</h3>
                <img class="img-dropdown" :class="!showDetailAppraise? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showDetailAppraise = !showDetailAppraise">
              </div>
            </div>
            <div class="card-body card-info" v-if="showDetailAppraise">
            <div class="container-table" v-if="appraises.asset_general && appraises.asset_general.length > 0">
                  <table class="table_detail_property">
                    <thead>
                    <tr>
                      <th>TT</th>
                      <th>Chỉ tiêu</th>
                      <th>TSTĐ</th>
                      <th v-for="(asset, index) in appraises.asset_general" :key="'header' + index" >{{ 'TSSS ' + asset.id }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                      <td>1</td>
                      <td>Loại tài sản</td>
                      <td>{{appraises.asset_type ? appraises.asset_type.description : "-"}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ asset.asset_type.description }}</td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>Thời điểm giao dịch</td>
                      <td>-</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ asset.transaction_type.description }}</td>
                    </tr>

                    <tr>
                      <td>3</td>
                      <td>Tọa độ</td>
                      <td><div style="max-width: 200px;">{{appraises.properties && appraises.properties.length > 0 ? appraises.properties[0].coordinates : "-"}}</div></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index"><div style="max-width: 200px;">{{ asset.coordinates ? asset.coordinates : "-"}}</div></td>
                    </tr>
                    <tr>
                      <td>4</td>
                      <td>Địa chỉ thửa đất</td>
                      <td>{{appraises.full_address_appraise}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index"><div style="max-width: 200px;">{{ asset.full_address ? asset.full_address : "-"}}</div></td>
                    </tr>
                    <tr>
                      <td>5</td>
                      <td>Pháp lý</td>
                      <td>{{appraises.properties && appraises.properties.length > 0 ? appraises.properties[0].legal.description : "-"}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{asset.properties && asset.properties.length > 0 && asset.properties[0].legal ? asset.properties[0].legal.description : "-" }}</td>
                    </tr>
                    <tr>
                      <td>6</td>
                      <td>Quy mô (Tổng diện tích)</td>
                      <td>Xem chi tiết ở bảng thông số diện tích và quy hoạch</td>
                      <td>Xem chi tiết ở bảng thông số diện tích và quy hoạch</td>
                      <td>Xem chi tiết ở bảng thông số diện tích và quy hoạch</td>
                      <td>Xem chi tiết ở bảng thông số diện tích và quy hoạch</td>
                    </tr>
                    <tr>
                      <td>7</td>
                      <td>Đơn giá theo quyết định</td>
                      <td>Xem chi tiết ở bảng thông số đơn giá đất UBND</td>
                      <td>Xem chi tiết ở bảng thông số đơn giá đất UBND</td>
                      <td>Xem chi tiết ở bảng thông số đơn giá đất UBND</td>
                      <td>Xem chi tiết ở bảng thông số đơn giá đất UBND</td>
                    </tr>
                    <tr>
                      <td>8</td>
                      <td>Chiều rộng</td>
                      <td>
                       <div v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'widthStreet' + index">{{`${comparison_factor.appraise_title}m`}}</div>
                      </td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetWidthStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'chieu_rong_mat_tien' +index">{{`${comparison_factor.asset_title}m`}}</span></td>
                    </tr>

                    <tr>
                      <td>9</td>
                      <td>Chiều dài</td>
                      <td>
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'widthLongLand' + index">{{`${comparison_factor.appraise_title}m`}}</div>
                      </td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetLongLand' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' + index">{{`${comparison_factor.asset_title}m`}}</span></td>
                    </tr>
                    <tr>
                      <td>10</td>
                      <td>Hình dáng</td>
                      <td>
                        <div v-if="appraises.properties && appraises.properties.length > 0 && appraises.properties[0].land_shape">{{appraises.properties[0].land_shape.description}}</div>
                        <div v-else>-</div>
                      </td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                        <div v-if="asset.properties && asset.properties.length > 0 && asset.properties[0].land_shape">{{asset.properties[0].land_shape.description}}</div>
                        <div v-else>-</div>
                      </td>
                    </tr>

                    <tr>
                      <td rowspan="4">11</td>
                      <td>Kết cấu xây dựng</td>
                      <td>
                        <div v-if="appraises.tangible_assets && appraises.tangible_assets.length > 0">{{appraises.tangible_assets[0].building_type.description}}</div>
                        <div v-else>-</div>
                      </td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                        <div v-if="asset.tangible_assets && asset.tangible_assets.length > 0">{{ asset.tangible_assets[0].building_type.description }}</div>
                        <div v-else>-</div>
                      </td>
                    </tr>

                    <tr>
                      <td>DTSXD</td>
                      <td>
                        <div v-if="appraises.tangible_assets && appraises.tangible_assets.length > 0">{{appraises.tangible_assets[0].total_construction_base}}m<sup>2</sup></div>
                        <div v-else>-</div>
                      </td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                        <div v-if="asset.tangible_assets && asset.tangible_assets.length > 0">{{ asset.tangible_assets[0].total_construction_base}}m<sup>2</sup></div>
                        <div v-else>-</div>
                      </td>
                    </tr>
                    <tr>
                      <td>Tỷ lệ CLCL</td>
                      <td>
                        <div v-if="appraises.tangible_assets && appraises.tangible_assets.length > 0">{{appraises.tangible_assets[0].remaining_quality}}%</div>
                        <div v-else>-</div>
                      </td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                        <div v-if="asset.tangible_assets && asset.tangible_assets.length > 0">{{ asset.tangible_assets[0].remaining_quality}}%</div>
                        <div v-else>-</div>
                      </td>
                    </tr>
                    <tr>
                      <td>Đơn giá xây dựng mới</td>
                      <td>-</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetConstructionAmount' + index">
                        {{asset.tangible_assets && asset.tangible_assets.length > 0 ? format(asset.tangible_assets[0].unit_price_m2) + 'đ' : '-' }}
                        </td>
                    </tr>

                    <tr>
                      <td>12</td>
                      <td>Giá trị xây dựng còn lại</td>
                      <td>-</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                        <div v-if="asset.tangible_assets && asset.tangible_assets.length > 0 && asset.tangible_assets[0]">{{remainingBuildingPrice(asset.tangible_assets[0])}}đ</div>
                        <div v-else>-</div>
                      </td>
                    </tr>

                    <tr>
                      <td>13</td>
                      <td>Vị trí</td>
                      <td>{{ (typeof appraises.properties !== 'undefined') && appraises.properties.length > 0 ? appraises.properties[0].description : '' }}</td>
                      <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'assetProperty' + indexAsset">
                        <span v-for="(property, index) in asset.properties" v-if="property.id === appraises.appraise_has_assets[indexAsset].asset_property_detail_id" :key="'property' +index">{{property.description}}</span>
                        <span v-else>-</span>
                      </td>
                    </tr>
                    <tr>
                      <td>14</td>
                      <td>Kết cấu giao thông</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'appraisalTraffic' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetTraffic' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ket_cau_duong' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>15</td>
                      <td>Độ rộng đường</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'appraisalStreet' + index">{{comparison_factor.appraise_title}}m</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +index">{{comparison_factor.asset_title}}m</span></td>
                    </tr>
                    <tr>
                      <td>16</td>
                      <td>Cơ sở hạ tầng</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'appraisalBasis' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetBasis' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>17</td>
                      <td>Lợi thế kinh doanh</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'appraisalBussiness' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetBussiness' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>18</td>
                      <td>An ninh, môi trường sống</td>
                     <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'appraisalSecurity' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>19</td>
                      <td>Phong thủy</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'appraisalSecurity' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>20</td>
                      <td>Điều kiện thanh toán</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'appraisalPay' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetPay' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>21</td>
                      <td>Giá rao bán</td>
                      <td></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'totalAmount' + index">{{ format(asset.total_amount) }} đ</td>
                    </tr>
                    <tr>
                      <td>22</td>
                      <td>Tỷ lệ giá rao bán</td>
                      <td></td>
                      <td v-for="(asset, index) in form.appraise_adapter" :key="'adjustPercent' + index">
                        <div class="d-flex">
                          <InputNumberFormat
                            class="label-none input_number_center"
                            v-model="asset.percent"
                            vid="number_legal"
                            label="Tỷ lệ"
                            :max="999999999"
                            :min="-999999999"
                            :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            @change="changePercentSaleRating($event, index)"
                          />
                          <div class="percent">%</div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>23</td>
                      <td>Tổng giá trị tài sản ước tính</td>
                      <td></td>
                      <td>{{format(totalPriceEstimate1)}}đ</td>
                      <td>{{format(totalPriceEstimate2)}}đ</td>
                      <td>{{format(totalPriceEstimate3)}}đ</td>
                    </tr>
                    <tr>
                      <td>24</td>
                      <td>Giá trị phần diện tích vi phạm quy hoạch</td>
                      <td></td>
                      <td>{{format(totalPriceViolationArea1)}}đ</td>
                      <td>{{format(totalPriceViolationArea2)}}đ</td>
                      <td>{{format(totalPriceViolationArea3)}}đ</td>
                    </tr>
                    <tr>
                      <td>25</td>
                      <td>Chi phí chuyển mục đích sử dụng</td>
                      <td></td>
                      <!-- <td>{{format(Math.abs(price1))}}đ</td>
                      <td>{{format(Math.abs(price2))}}đ</td>
                      <td>{{format(Math.abs(price3))}}đ</td> -->
                      <td v-for="(asset, index) in form.appraise_adapter" :key="'changePurposePrice' + index">
                         <InputNumberFormat
                            class="label-none input_number_center"
                            v-model="asset.change_purpose_price"
                            vid="change_purpose_price"
                            label="giá chuyển đổi"
                            :max="999999999999999"
                            :min="-999999999999999"
                            :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            @change="changeChangePurposePrice($event, index)"
                          />
                      </td>
                    </tr>
                    <tr>
                      <td>26</td>
                      <td>Giá trị QSDĐ {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}} ước tính</td>
                      <td></td>
                      <td>{{format(totalPrice1)}}</td>
                      <td>{{format(totalPrice2)}}</td>
                      <td>{{format(totalPrice3)}}</td>
                    </tr>
                    <tr>
                      <td>27</td>
                      <td>Đ/giá {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}} B.Quân.</td>
                      <td></td>
                      <td>{{format(dgd1)}}</td>
                      <td>{{format(dgd2)}}</td>
                      <td>{{format(dgd3)}}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
            </div>
            <!---------------------------------------BẢNG ĐIỀU CHỈNH CÁC YẾU TỐ SO SÁNH TSTĐG VÀ TSSS---------------------------------->
            <div class="marginTop card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">BẢNG ĐIỀU CHỈNH CÁC YẾU TỐ SO SÁNH TSTĐ VÀ TSSS</h3>
                <img class="img-dropdown" :class="!showDetailComparison? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showDetailComparison = !showDetailComparison">
              </div>
            </div>
            <div class="card-body card-info" v-if="showDetailComparison">
              <div class="container-table" v-if="typeof appraises.asset_general !== 'undefined' && appraises.asset_general.length > 0">
                  <table class="table-comparision">
                    <thead>
                      <tr>
                        <th>TT</th>
                        <th>Yếu tố so sánh</th>
                        <th>TSTĐ</th>
                        <th v-for="(asset, index) in appraises.comparison_factor" :key="'headerElement' + index" >{{ 'TSSS ' + asset.id }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Đơn giá quyền sử dụng đất (đồng/m<sup>2</sup>)</td>
                        <td>Chưa biết</td>
                        <td>{{format(dgd1)}}</td>
                        <td>{{format(dgd2)}}</td>
                        <td>{{format(dgd3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1" :key="'appraisalLegalRow' + index">
                        <td rowspan="4">A</td>
                        <td><strong>Pháp lý</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly'" :key="'appraisalLegal' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetInfo' + index"><span  v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'phap_ly'" :key="'phap_ly' + indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1" :key="'changeLegalRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexLegal) in appraises.asset_general" :key="'inputLegal' + indexLegal">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexLegal].comparison_factor" v-if="comparison_factor.type === 'phap_ly'" :key="'phap_ly' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexLegal, index, 'phap_ly')"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1" :key="'LegalRow1' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(pricePl1)}}</td>
                        <td>{{format(pricePl2)}}</td>
                        <td>{{format(pricePl3)}}</td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1" :key="'LegalRow2' + index">
                        <td><strong>Giá sau điều chỉnh</strong></td>
                        <td></td>
                        <td><strong>{{format(totalPricePL1)}}</strong></td>
                        <td><strong>{{format(totalPricePL2)}}</strong></td>
                        <td><strong>{{format(totalPricePL3)}}</strong></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1" :key="'quymo' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('quy_mo')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Quy mô</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_mo'" :key="'quymo1' + index">{{comparison_factor_appraise.appraise_title}}m<sup>2</sup></td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetQuymo' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'quy_mo'" :key="'quymo2' +indexItem">{{comparison_factor.asset_title}}m<sup>2</sup></span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1" :key="'quymo3' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputQuymo' + indexStreet">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'quy_mo'" :key="'quymo4' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexStreet, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1" :key="'quymo5' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceQm1)}}</td>
                        <td>{{format(priceQm2)}}</td>
                        <td>{{format(priceQm3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1" :key="'appraisalWidthRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('chieu_rong_mat_tien')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Chiều rộng mặt tiền</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_rong_mat_tien'" :key="'appraisalWidth' + index">{{comparison_factor_appraise.appraise_title}}m</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'chieu_rong_mat_tien' +indexItem">{{comparison_factor.asset_title}}m</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1" :key="'WidthRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexWidth) in appraises.asset_general" :key="'inputWidth' + indexWidth">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexWidth].comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'chieu_rong_mat_tien' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexWidth, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1" :key="'WidthChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceCrmt1)}}</td>
                        <td>{{format(priceCrmt2)}}</td>
                        <td>{{format(priceCrmt3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1" :key="'appraisalDepthRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('chieu_sau_khu_dat')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Chiều sâu khu đất</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_sau_khu_dat'" :key="'appraisalDepth' + index">{{comparison_factor_appraise.appraise_title}}m</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' +indexItem">{{comparison_factor.asset_title}}m</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1" :key="'DepthRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexDepth) in appraises.asset_general" :key="'inputDepth' + indexDepth">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexDepth].comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexDepth, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1" :key="'DepthChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceCskd1)}}</td>
                        <td>{{format(priceCskd2)}}</td>
                        <td>{{format(priceCskd3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1" :key="'appraisalLandRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('hinh_dang_dat')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Hình dáng</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'hinh_dang_dat'" :key="'appraisalLand' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'hinh_dang_dat'" :key="'hinh_dang_dat' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1" :key="'LandRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexLand) in appraises.asset_general" :key="'inputLand' + indexLand">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexLand].comparison_factor" v-if="comparison_factor.type === 'hinh_dang_dat'" :key="'hinh_dang_dat' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexLand, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'hinh_dang_dat' && comparison_factor_appraise.status === 1" :key="'LandChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceHdd1)}}</td>
                        <td>{{format(priceHdd2)}}</td>
                        <td>{{format(priceHdd3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1" :key="'appraisalTrafficRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('giao_thong')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                          </td>
                        <td><strong>Giao thông</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'giao_thong'" :key="'appraisalTraffic' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'giao_thong'" :key="'giao_thong' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1" :key="'TrafficRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexTraffic) in appraises.asset_general" :key="'inputTraffic' + indexTraffic">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexTraffic].comparison_factor" v-if="comparison_factor.type === 'giao_thong'" :key="'giao_thong' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexTraffic, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1" :key="'TrafficChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceGt1)}}</td>
                        <td>{{format(priceGt2)}}</td>
                        <td>{{format(priceGt3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1" :key="'ketcauduong' + index">
                        <td rowspan="3">

                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('ket_cau_duong')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Kết cấu đường</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'ket_cau_duong'" :key="'ketcauduong1' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetKetcauduong' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ketcauduong2' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1" :key="'ketcauduong3' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputKetcauduong' + indexStreet">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ketcauduong4' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexStreet, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1" :key="'ketcauduong5' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceKcd1)}}</td>
                        <td>{{format(priceKcd2)}}</td>
                        <td>{{format(priceKcd3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1" :key="'appraisalStreetRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('do_rong_duong')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Độ rộng đường</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'do_rong_duong'" :key="'appraisalStreet' + index">{{comparison_factor_appraise.appraise_title}}m</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +indexItem">{{comparison_factor.asset_title}}m</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1" :key="'StreetRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputStreet' + indexStreet">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexStreet, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1" :key="'StreetChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceDrd1)}}</td>
                        <td>{{format(priceDrd2)}}</td>
                        <td>{{format(priceDrd3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1" :key="'appraisalConditionsRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('dieu_kien_ha_tang')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Điều kiện hạ tầng</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_ha_tang'" :key="'appraisalConditions' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1" :key="'ConditionsRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexConditions) in appraises.asset_general" :key="'inputConditions' + indexConditions">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexConditions].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexConditions, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1" :key="'ConditionsChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceDkht1)}}</td>
                        <td>{{format(priceDkht2)}}</td>
                        <td>{{format(priceDkht3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1" :key="'appraisalBusinessRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('kinh_doanh')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Kinh doanh</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'kinh_doanh'" :key="'appraisalBusiness' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1" :key="'BusinessRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexBusiness) in appraises.asset_general" :key="'inputBusiness' + indexBusiness">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexBusiness].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexBusiness, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1" :key="'BusinessChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceKd1)}}</td>
                        <td>{{format(priceKd2)}}</td>
                        <td>{{format(priceKd3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1" :key="'appraisalSecurityRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('an_ninh_moi_truong_song')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>An ninh, môi trường sống</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'an_ninh_moi_truong_song'" :key="'appraisalSecurity' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1" :key="'SecurityRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexSecurity) in appraises.asset_general" :key="'inputSecurity' + indexSecurity">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexSecurity].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexSecurity, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1" :key="'SecurityChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceAnmts1)}}</td>
                        <td>{{format(priceAnmts2)}}</td>
                        <td>{{format(priceAnmts3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1" :key="'appraisalFengShuiRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('phong_thuy')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Phong thủy</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phong_thuy'" :key="'appraisalFengShui' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1" :key="'FengShuiRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexFengShui) in appraises.asset_general" :key="'inputFengShui' + indexFengShui">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexFengShui].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexFengShui, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1" :key="'FengShuiChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(pricePt1)}}</td>
                        <td>{{format(pricePt2)}}</td>
                        <td>{{format(pricePt3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1" :key="'quyhoach' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('quy_hoach')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Quy hoạch/Hiện trạng</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_hoach'" :key="'quyhoach1' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetQuyhoach' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'quy_hoach'" :key="'quyhoach2' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1" :key="'quyhoach3' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputQuyhoach' + indexStreet">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'quy_hoach'" :key="'quyhoach4' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexStreet, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1" :key="'quyhoach5' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceQh1)}}</td>
                        <td>{{format(priceQh2)}}</td>
                        <td>{{format(priceQh3)}}</td>
                      </tr>

                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1" :key="'appraisalPayRow' + index">
                        <td rowspan="3">
                          <button class="btn-delete" type="button" @click="dialogDeleteComparisionDefault('dieu_kien_thanh_toan')"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
                        </td>
                        <td><strong>Điều kiện thanh toán</strong></td>
                        <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_thanh_toan'" :key="'appraisalPay' + index">{{comparison_factor_appraise.appraise_title}}</td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1" :key="'PayRow' + index">
                        <td>Tỷ lệ điều chỉnh</td>
                        <td></td>
                        <td v-for="(asset, indexPay) in appraises.asset_general" :key="'inputPay' + indexPay">
                          <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexPay].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +index">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="comparison_factor.adjust_percent"
                              vid="number_legal"
                              label="Tỷ lệ"
                              :min="-100"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLegalRate($event, indexPay, index)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1" :key="'PayChangeRow' + index">
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td>{{format(priceDktt1)}}</td>
                        <td>{{format(priceDktt2)}}</td>
                        <td>{{format(priceDktt3)}}</td>
                      </tr>
                    <tr v-if="showOtherFactor && data_other_comparison && data_other_comparison.length > 0"
                          v-for="(data_appraise, index) in data_other_comparison || []"
                          :key="'yeu_to_khac' + index">
                        <td style="padding: unset" colspan="6">
                          <tr>
                            <td rowspan="3">
                              <button class="btn-delete" type="button" @click="handleDialogDeleteComparision(index, data_appraise.other_factor_asset)"><img src="../../../../assets/icons/ic_delete.svg" style=" color: red" alt="save"></button>
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
                                v-model="data_appraise.appraise_title"
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
                                <InputNumberFormat
                                  class="label-none input_number_center"
                                  v-model="rate_asset.adjust_percent"
                                  vid="number_legal"
                                  label="Tỷ lệ"
                                  :min="-100"
                                  :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                  @change="changeOtherRate($event, rate_asset)"
                                />
                              </div>
                            </td>
                          </tr>
                          <tr v-if="showOtherFactor">
                            <td class="td_other_appraise">Mức điều chỉnh</td>
                            <td class="td_other_asset_title"></td>
                            <td class="td_other_asset_title" v-for="(price_other, index) in price_other_comparison ? price_other_comparison[index] : []" :key="'yeutokhacprice' +index">
                              {{format(price_other.indication_price_asset) || 0}}
                            </td>
                          </tr>
                        </td>
                    </tr>

                      <tr  class="other_button_container" >
                        <td class="td_none"></td>
                        <td class="td_none">
                          <button class="btn btn-orange-other other_button" type="button" @click="handleAddOtherFactor" >Thêm yếu tố khác</button>
                        </td>
                      </tr>

                      <tr>
                        <td>2</td>
                        <td colspan="2">Mức giá chỉ dẫn (đồng/m<sup>2</sup>)</td>
                        <td>{{format(mgcd1)}}</td>
                        <td>{{format(mgcd2)}}</td>
                        <td>{{format(mgcd3)}}</td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td colspan="2">Mức giá trung bình của các mức giá chỉ dẫn (đồng/m<sup>2</sup>)</td>
                        <td align="center" colspan="3">{{format(mgtb)}}</td>
                      </tr>
                      <tr>
                        <td>4</td>
                        <td colspan="2" :class=" (mgcl1 > 15 || mgcl2 > 15 || mgcl3 > 15)  || (mgcl1 < -15 || mgcl2 < -15 || mgcl3 < -15) ? 'text-danger' : ''">
                          Mức độ chênh lệch với mức giá trung bình của các mức giá chỉ dẫn (%)
                        </td>
                        <td>{{mgcl1 + '%'}}</td>
                        <td>{{mgcl2 + '%'}}</td>
                        <td>{{mgcl3 + '%'}}</td>
                      </tr>
                      <tr>
                        <td>5</td>
                        <td colspan="2">Tổng giá trị điều chỉnh gộp</td>
                        <td>{{format(Math.abs(tldc1))}}</td>
                        <td>{{format(Math.abs(tldc2))}}</td>
                        <td>{{format(Math.abs(tldc3))}}</td>
                      </tr>
                      <tr >
                        <td>6</td>
                        <td colspan="2">Tổng số lần điều chỉnh (lần)</td>
                        <td>{{format(comparisonFactorChange1)}}</td>
                        <td>{{format(comparisonFactorChange2)}}</td>
                        <td>{{format(comparisonFactorChange3)}}</td>
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
                        <td colspan="2">Tổng giá trị điều chỉnh thuần(đ/m<sup>2</sup>)</td>
                        <td>{{format(tldc1)}}</td>
                        <td>{{format(tldc2)}}</td>
                        <td>{{format(tldc3)}}</td>
                      </tr>
                      <tr>
                        <td>9</td>
                        <td colspan="2">
                          <div class="d-flex col-12 col-lg-12">
                            <div class="col-4 col-lg-4"></div>
                            <div class="d-flex col-8 col-lg-8">
                              <strong style="margin-top: 2px; margin-right: 30px">Thống nhất mức giá chỉ dẫn</strong>
                              <InputSwitchLayerCuting
                                v-model="layer_cutting_procedure"
                                vid="layer_cutting_procedure"
                                @input="changeLayerCuttingProcedure"
                              />
                            </div>
                          </div>
                        </td>
                        <td colspan="3" align="center"><strong>{{format(mgtn)}}</strong></td>
                      </tr>
                      <tr v-if="layer_cutting_procedure">
                        <td>9.1</td>
                        <td colspan="2"> Đơn giá sau cắt lớp</td>
                        <td colspan="3" align="center">
                          <div style="padding: 0 10rem">
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="layer_cutting_procedure_price"
                              vid="layer_cutting_procedure_price"
                              label="price"
                              :max="999999999999999"
                              :min="-999999999999999"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeLayerCuttingPrice($event)"
                            />
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>10</td>
                        <td colspan="2">
                            <div class="d-flex col-12 col-lg-12 container_round">
                                <p class="title_round">Làm tròn</p>
                                <div style="width: 50px">
                                  <InputNumberFormat
                                    v-model="round_total"
                                    vid="round_total"
                                    :max="99999999"
                                    :min="-99999999"
                                    @change="changeRoundTotal($event)"
                                  />
                                </div>
                            </div>
                        </td>
                        <td colspan="3" align="center">{{layer_cutting_procedure ? format(formatCurrent(round_layer_cutting_produre)) : format(formatCurrent(mgtn))}}đ</td>
                      </tr>

                      <tr v-if="appraises.properties[0].property_detail && appraises.properties[0].property_detail.length > 1">
                        <td>11</td>
                        <td colspan="2">
                          <div>
                            Đơn giá đất {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).land_type_purpose.acronym}} thị trường
                          </div>
                          <div>({{type_method}})</div>
                        </td>
                        <td>
                          <div>
                            <InputNumberFormat
                              class="label-none input_number_center"
                              v-model="remaining_commerce_price"
                              vid="remaining_commerce_price"
                              label="price"
                              :max="999999999999999"
                              :min="-999999999999999"
                              :disabledInput="checkProcedure"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeRemaingCommercePrice($event)"
                            />
                          </div>
                        </td>
                        <td>
                          <div style="padding-left:20%; height:30px" class="d-flex col-12 col-lg-12 container_round">
                            <p class="title_round">Làm tròn</p>
                            <div style="width: 50px">
                              <InputNumberFormat
                                v-model="round_composite"
                                vid="round_composite"
                                :max="99999999"
                                :min="-99999999"
                                @change="changeRoundCompositeLand($event)"
                              />
                            </div>
                          </div>
                        </td>
                        <td>{{ format(formatRoundComposite(remaining_commerce_price))}}đ</td>
                      </tr>

                      <tr v-if="show_violation_price && appraises.properties[0].property_detail && appraises.properties[0].property_detail.length && appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).is_zoning">
                        <td>{{appraises.properties[0].property_detail && appraises.properties[0].property_detail.length > 1 ? 12 : 11}}</td>
                        <td colspan="2">
                          <div>
                            Đơn giá đất {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}} vi phạm quy hoạch
                          </div>
                          <div>({{type_violation_method}})</div>
                        </td>
                        <td>{{format(violation_facility_price)}}đ</td>
                        <td>
                          <div style="padding-left:20%; height:30px" class="d-flex col-12 col-lg-12 container_round">
                            <p class="title_round">Làm tròn</p>
                            <div style="width: 50px">
                              <InputNumberFormat
                                v-model="round_violation_facility"
                                vid="round_violation_facility"
                                :max="99999999"
                                :min="-99999999"
                                @change="changeRoundViolationFacility($event)"
                              />
                            </div>
                          </div>
                        </td>
                        <td>{{ format(formatRoundViolationFacility(violation_facility_price))}}đ</td>
                      </tr>

                      <tr v-if="appraises.properties[0].property_detail && appraises.properties[0].property_detail.length > 1 && show_violation_price && appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).is_zoning">
                        <td>{{appraises.properties[0].property_detail && appraises.properties[0].property_detail.length && appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).is_zoning ? 13 : 12}}</td>
                        <td colspan="2">
                          <div>
                            Đơn giá đất {{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).land_type_purpose.acronym}} vi phạm quy hoạch
                          </div>
                        <div>({{type_violation_method}})</div>
                        </td>
                        <td>{{format(violation_composite_price)}}đ</td>
                        <td>
                          <div style="padding-left:20%; height:30px" class="d-flex col-12 col-lg-12">
                            <p class="title_round">Làm tròn</p>
                            <div style="width: 50px">
                              <InputNumberFormat
                                v-model="round_violation_composite"
                                vid="round_violation_composite"
                                :max="99999999"
                                :min="-99999999"
                                @change="changeRoundViolationComposite($event)"
                              />
                            </div>
                          </div>
                        </td>
                        <td>{{ format(formatRoundViolationComposite(violation_composite_price))}}đ</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
            <!--------------------------- KẾT QUẢ THẨM ĐỊNH GIÁ ----------------------------------->
            <!-- <div class="marginTop card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title text-nowrap">KẾT QUẢ THẨM ĐỊNH GIÁ</h3>
                <img class="img-dropdown" :class="!showResultAppraise? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showResultAppraise = !showResultAppraise">
              </div>
            </div>
            <div class="card-body card-info" v-if="showResultAppraise">
              <div class="container-table" v-if="appraises.asset_general && appraises.asset_general.length > 0">
                <table class="table_result_price">
                  <thead>
                    <tr>
                      <th>Tên tài sản</th>
                      <th>MĐSD</th>
                      <th>Diện tích (m<sup>2</sup>)</th>
                      <th>Đơn giá</th>
                      <th>Thành tiền (VNĐ)</th>
                    </tr>
                  </thead>
                  <tbody> -->
                    <!---------------------------------------------- phần Diện tích PHÙ HỢP quy hoạch--------------------------------------------->
                    <!-- <tr v-if="showApproriateArea(appraises.properties[0]) !== 0">
                      <td :rowspan="appraises.properties[0].property_detail && appraises.properties[0].property_detail.length > 1 && showRemainingArea(appraises.properties[0]) !== 0 ? 2 : 1">Phần diện tích đất phù hợp quy hoạch</td>
                      <td>{{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}}</td>
                      <td>{{showApproriateArea(appraises.properties[0])}}</td>
                      <td>{{layer_cutting_procedure ? format(formatCurrent(round_layer_cutting_produre)) : format(formatCurrent(mgtn))}}</td>
                      <td>{{format(showPriceApproriateFacility(appraises.properties[0]))}}</td>
                    </tr>
                    <tr v-if="showRemainingArea(appraises.properties[0]) !== 0">
                      <td v-if="showApproriateArea(appraises.properties[0]) === 0">Phần diện tích đất phù hợp quy hoạch</td>
                      <td>{{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).land_type_purpose.acronym}}</td>
                      <td>{{showRemainingArea(appraises.properties[0])}}</td>
                      <td>{{format(formatRoundComposite(remaining_commerce_price))}}</td>
                      <td>{{format(showPriceApproriateRemaining(appraises.properties[0]))}}</td>
                    </tr> -->
                    <!----------------------------------------------- phần Diện tích VI PHẠM quy hoạch ----------------------------------------------->
                    <!-- <tr v-if="+appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).planning_area !== 0" >
                      <td
                        :rowspan="appraises.properties[0].property_detail && appraises.properties[0].property_detail.length > 1 &&
                        +appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).planning_area !== 0 ? 2 : 1">
                        Phần diện thích đất vi phạm quy hoạch
                      </td>
                      <td>{{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym}}</td>
                      <td>{{+appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).planning_area}}</td>
                      <td>{{format(formatRoundViolationFacility(violation_facility_price))}}</td>
                      <td>{{format(showPriceViolationFacility(appraises.properties[0]))}}</td>
                    </tr>
                    <tr v-if="appraises.properties[0].property_detail && appraises.properties[0].property_detail.length > 1 && +appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).planning_area !== 0">
                      <td v-if="+appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).planning_area === 0">Phần diện thích đất vi phạm quy hoạch</td>
                      <td>{{appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).land_type_purpose.acronym}}</td>
                      <td>{{+appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).planning_area}}</td>
                      <td>{{format(formatRoundViolationComposite(violation_composite_price))}}</td>
                      <td>{{format(showPriceViolationRemaining(appraises.properties[0]))}}</td>
                    </tr>
                    <tr>
                      <td colspan="4"><strong>Tổng cộng</strong></td>
                      <td>
                        <strong>
                          {{format(result_price_appropriate_facility + result_price_appropriate_remaining + result_price_violation_facility + result_price_violation_remaining)}}
                        </strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div> -->
            <h3 class="mt-4 text-center" v-if="appraises.asset_general && appraises.asset_general.length === 0">Không có tài sản so sánh để thực hiện điều chỉnh</h3>
          </div>
          <ModalDelete
            v-if="openModalDelete"
            @cancel="openModalDelete = false"
            @action="handleDeleteOtherFactor"
          />
          <ModalDelete
            v-if="openMdodalDeleteDefault"
            @cancel="openMdodalDeleteDefault = false"
            @action="actionDeleteComparisionDefault"
          />
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange" type="button" @click="saveData"><img src="../../../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save">Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel(formData.id)"><img src="../../../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save">Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import {BDropdown, BDropdownItem, BTooltip} from 'bootstrap-vue'
import {Tabs, TabItem} from 'vue-material-tabs'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputText from '@/components/Form/InputText'
import InputSwitchLayerCuting from '@/components/Form/InputSwitchLayerCuting'
import Certificate from '@/models/Certificate'
import ModalDelete from '@/components/Modal/ModalDelete'
export default {
	name: 'ModalConstructionUnit',
	props: ['formData'],
	data () {
		return {
			appraises: [],
			form: this.formData !== 'undefined' && this.formData !== '' ? this.formData : {},
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: [],
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
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
			// total_appropriate_asset_area_1: 0,
			// total_appropriate_asset_area_2: 0,
			// total_appropriate_asset_area_3: 0,
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
			result_price_violation_remaining: 0
		}
	},
	components: {
		InputNumberFormat,
		Tabs,
		TabItem,
		InputText,
		ModalDelete,
		InputSwitchLayerCuting,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip
	},
	computed: {
	},
	created () {
		this.getOtherComparison()
		this.getDataLand()
		this.calculation(this.form)
		this.getData()
		this.getDataPrice(this.form.asset_unit_price)
	},
	mounted () {
		// this.getOtherComparison()
	},
	methods: {
		showPriceApproriateFacility (property) {
			if (property) {
				if (this.layer_cutting_procedure) {
					let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
					let price_layer_cutting = this.formatCurrent(this.round_layer_cutting_produre)
					this.result_price_appropriate_facility = (price_layer_cutting / 1).toFixed(0) * (facilityProperty.total_area - facilityProperty.planning_area)
					return this.result_price_appropriate_facility
				} else {
					let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
					let price_mgtn = this.formatCurrent(this.mgtn)
					this.result_price_appropriate_facility = (price_mgtn / 1).toFixed(0) * (facilityProperty.total_area - facilityProperty.planning_area)
					return this.result_price_appropriate_facility
				}
			} else return 0
		},
		showPriceApproriateRemaining (property) {
			if (property) {
				let remaingProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === false)
				let remaining_price = this.formatRoundComposite(this.remaining_commerce_price)
				this.result_price_appropriate_remaining = (remaining_price / 1).toFixed(0) * (+remaingProperty.total_area - +remaingProperty.planning_area)
				return this.result_price_appropriate_remaining
			} else return 0
		},
		showPriceViolationFacility (property) {
			if (property) {
				let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
				let vio_facility_price = this.formatRoundViolationFacility(this.violation_facility_price)
				this.result_price_violation_facility = (vio_facility_price / 1).toFixed(0) * facilityProperty.planning_area
				return this.result_price_violation_facility
			} else return 0
		},
		showPriceViolationRemaining (property) {
			if (property) {
				let remaingProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === false)
				let vio_remaining_price = this.formatRoundViolationComposite(this.violation_composite_price)
				this.result_price_violation_remaining = (vio_remaining_price / 1).toFixed(0) * remaingProperty.planning_area
				return this.result_price_violation_remaining
			} else return 0
		},
		showApproriateArea (property) {
			if (property) {
				let facilityProperty = property.property_detail.find(property_detail => property_detail.is_transfer_facility === true)
				if (facilityProperty) {
					return facilityProperty.total_area - facilityProperty.planning_area
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
		validateArea (areaValue) {
			if (areaValue < 0) {
				this.checkValidArea = true
			}
			return parseFloat(areaValue).toFixed(2)
		},
		async changeViolationLand (event, dataItemLand) {
			await this.form.asset_unit_area.forEach(item => {
				if (item.asset_general_id === dataItemLand.asset_general_id && item.land_type_id === dataItemLand.land_type_id) {
					if (+event) {
						item.violation_asset_area = parseFloat(+event).toFixed(2)
					} else item.violation_asset_area = 0
				}
			})
			await this.calculationChangePrice(this.form)
			await this.calculation(this.form)
		},
		// get data total area scale
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
									total_area: property_asset_detail.total_area - +getDataUnitPrice[0].violation_asset_area,
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
						appropriate_appraise_land: +property_appraise.total_area - +property_appraise.planning_area,
						asset_general_land: []
					})
					// lay dien tich vi pham quy hoach (TSTD)
					this.violation_zoning_land.push({
						name_purpose_land: property_appraise.land_type_purpose.acronym,
						violation_appraise_land: +property_appraise.planning_area,
						asset_general_land: []
					})
					this.total_appraise_area += +property_appraise.total_area - +property_appraise.planning_area
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
		changeLayerCuttingProcedure (event) {
			if (event) {
				if (this.form.layer_cutting_price) {
					this.layer_cutting_procedure_price = this.form.layer_cutting_price
				}
				this.round_layer_cutting_produre = this.layer_cutting_procedure_price
				this.mgtnTemp = this.layer_cutting_procedure_price
			}
			this.calculation(this.form)
		},
		changeLayerCuttingPrice (event) {
			if (event) {
				this.layer_cutting_procedure_price = parseFloat(event).toFixed(0)
			} else this.layer_cutting_procedure_price = ''
			this.mgtnTemp = this.layer_cutting_procedure_price ? this.layer_cutting_procedure_price : 0
			this.calculation(this.form)
		},
		remainingBuildingPrice (tangible_assets) {
			if (tangible_assets && tangible_assets.unit_price_m2 && tangible_assets.remaining_quality && tangible_assets.total_construction_base) {
				return this.format((tangible_assets.unit_price_m2 * tangible_assets.total_construction_base) * tangible_assets.remaining_quality / 100)
			} else return '-'
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
			this.form.comparison_factor = await tempArrayComparison
			await this.calculation(this.form)
		},
		async dialogDeleteComparisionDefault (type) {
			this.openMdodalDeleteDefault = true
			this.typeComparision = type
		},
		changeRemaingCommercePrice (event) {
			if (event) {
				this.remaining_commerce_price = parseFloat(event).toFixed(0)
			} else this.remaining_commerce_price = ''
		},
		async changePercentSaleRating (event, index) {
			if (event) {
				this.form.appraise_adapter[index].percent = event
			} else this.form.appraise_adapter[index].percent = 0
			await this.calculation(this.form)
		},
		changeChangePurposePrice (event, index) {
			if (event) {
				this.form.appraise_adapter[index].change_purpose_price = event
			} else this.form.appraise_adapter[index].change_purpose_price = 0
			this.calculation(this.form)
		},
		// lam tron dat co so
		changeRoundTotal (event) {
			if (event || event === 0) {
				this.round_total = parseFloat(event).toFixed(0)
				this.mgtnTemp = this.formatCurrent(this.mgtn)
			} else {
				this.round_total = 0
				this.mgtnTemp = this.mgtn
			}
			this.calculation(this.form)
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
		checkDataPriceUBND (asset, pricePurposeLands) {
			let empty = true
			pricePurposeLands.forEach(item => {
				if (item.asset_general_id === asset.id) {
					empty = false
				}
			})
			return empty
		},
		getDataPrice (data) {
			this.form.asset_unit_price.forEach((item, index) => {
				if (!item.update_value) {
					this.form.asset_unit_price[index].update_value = item.original_value
				}
			})
			let formDataClone = JSON.parse(JSON.stringify(this.formData))
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
		},
		async changePriceLand (event, dataItem) {
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
			await this.calculationChangePrice(this.form)
			await this.calculation(this.form)
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
			if (positionTem.length === 0 || max === -Infinity || max === Infinity) {
				this.otherIndex = 0
			} else this.otherIndex = max
			await this.calculation(this.form)
		},
		getDataOtherFactor () {
			if (this.other_comparison.length > 0) {
				this.showOtherFactor = true
			}
			this.form.asset_general.forEach(asset => {
				const other_comparison_factor = this.other_comparison.filter(item => item.asset_general_id === asset.id)
				this.data_other_comparison.push({
					id: asset.id,
					other_factor: other_comparison_factor
				})
			})
		},
		calculation (asset) {
			let arrayPriceTem = []
			// thông tin TSSS
			let asset1 = (typeof asset.asset_general[0] !== 'undefined') ? asset.asset_general[0] : null
			let asset2 = (typeof asset.asset_general[1] !== 'undefined') ? asset.asset_general[1] : null
			let asset3 = (typeof asset.asset_general[2] !== 'undefined') ? asset.asset_general[2] : null

			let asset_percent1 = (typeof asset.appraise_adapter[0] !== 'undefined') ? asset.appraise_adapter[0].percent : null
			let asset_percent2 = (typeof asset.appraise_adapter[1] !== 'undefined') ? asset.appraise_adapter[1].percent : null
			let asset_percent3 = (typeof asset.appraise_adapter[2] !== 'undefined') ? asset.appraise_adapter[2].percent : null

			// lấy diện tích sàn xây dựng
			this.dtsxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].total_construction_base : 0
			this.dtsxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].total_construction_base : 0
			this.dtsxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].total_construction_base : 0
			// lấy property của TSSS
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
			// --------------------------------Tính diện tích đất TSSS (Quy mô) -----------------------------------------//
			// Tính tổng diện tích đất vi phạm quy hoạch
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
				// Tính tổng diện tích đất phù hợp quy hoạch
				// this.form.asset_general.forEach(asset => {
				//   asset.properties.forEach(property_asset => {
				//     let appraiseHasAsset = this.form.appraise_has_assets.filter(item => item.asset_property_detail_id === property_asset.id)
				//     if (appraiseHasAsset && appraiseHasAsset.length > 0) {
				//     }
				//   })
				// })
				// this.total_appropriate_asset_area_1 = 0
				// this.total_appropriate_asset_area_2 = 0
				// this.total_appropriate_asset_area_3 = 0
				// Tính diện tích đất phù hợp quy hoạch khi thay đổi diện tích vi phạm quy hoạch
			})
			// xóa dữ liệu cũ
			this.asset_appropriate_area_arr = []
			// quét update lại asset_appropriate_area_arr
			this.form.asset_general.forEach(asset => {
				asset.properties.forEach(property_asset => {
					let appraiseHasAsset = this.form.appraise_has_assets.filter(item => item.asset_property_detail_id === property_asset.id)
					if (appraiseHasAsset && appraiseHasAsset.length > 0) {
						// lấy giá trị tổng diện tích đất phù hợp
						this.arrayTotalAppropriateArea[asset.id] = property_asset.asset_general_land_sum_area - this.arrayTotalViolationArea[asset.id]
						property_asset.property_detail.forEach(property_asset_detail => {
							// lấy giá trị đất vi phạm quy hoạch từ asset_unit_area
							let getDataUnitPrice = this.form.asset_unit_area.filter(itemVioLand => itemVioLand.asset_general_id === property_asset.asset_general_id && itemVioLand.land_type_id === property_asset_detail.land_type_purpose)
							// push diện tích đất phù hợp quy hoạch
							if (getDataUnitPrice && getDataUnitPrice.length > 0) {
								this.asset_appropriate_area_arr.push({
									asset_general_id: property_asset.asset_general_id,
									total_area: property_asset_detail.total_area - +getDataUnitPrice[0].violation_asset_area,
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
					// check tồn tại trong đất phù hợp quy hoạch
					let checkExist = this.appropriate_zoning_land.filter(checkItem => checkItem.name_purpose_land === itemLandAsset.name_purpose_land_asset)
					if (checkExist && checkExist.length > 0) {
						// thêm mục đất của TSSS vào mục đích đất đã tồn tại
						this.appropriate_zoning_land.forEach((appropriateLand, index) => {
							if (appropriateLand.name_purpose_land === itemLandAsset.name_purpose_land_asset) {
								this.appropriate_zoning_land[index]['asset_general_land'] = dataLand
							}
						})
					} else {
						// thêm mục đất của TSSS có mà TSTD k có
						this.appropriate_zoning_land.push({
							name_purpose_land: itemLandAsset.name_purpose_land_asset,
							appropriate_appraise_land: 0,
							asset_general_land: dataLand
						})
					}
				}
			})

			// tính Đơn giá xây dựng của TSSS
			this.dgxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].unit_price_m2 : 0
			this.dgxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].unit_price_m2 : 0
			this.dgxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].unit_price_m2 : 0

			// lấy tỉ lệ Chất lượng còn lại của TSSS
			this.clcl1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].remaining_quality : 0
			this.clcl2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].remaining_quality : 0
			this.clcl3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].remaining_quality : 0

			// lấy giá cơ sở
			this.baseUnitPrice = 0 // giá đất cơ sở của UBND
			this.baseAcronym = '' // name tắt của loại đất
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

			// --------------------------- Tính đơn giá diện tích vi phạm quy hoạch ---------------------------//
			// đơn giá diện tích vi phạm quy hoạch TTSS-1
			this.totalPriceViolationArea1 = 0
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
					this.totalPriceViolationArea1 = price_violation1.reduce((a, b) => a + b, 0)
					price_violation1 = []
				} else if (price_violation1.length > 0 && price_violation1.length === 1) {
					this.totalPriceViolationArea1 = price_violation1[0]
					price_violation1 = []
				}
			})
			// đơn giá diện tích vi phạm quy hoạch TTSS-2
			this.totalPriceViolationArea2 = 0
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
					this.totalPriceViolationArea2 = price_violation2.reduce((a, b) => a + b, 0)
					price_violation2 = []
				} else if (price_violation2.length > 0 && price_violation2.length === 1) {
					this.totalPriceViolationArea2 = price_violation2[0]
					price_violation2 = []
				}
			})
			// đơn giá diện tích vi phạm quy hoạch TTSS-3
			this.totalPriceViolationArea3 = 0
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
					this.totalPriceViolationArea3 = price_violation3.reduce((a, b) => a + b, 0)
					price_violation3 = []
				} else if (price_violation3.length > 0 && price_violation3.length === 1) {
					this.totalPriceViolationArea3 = price_violation3[0]
					price_violation3 = []
				}
			})
			// khai báo biến Chi phí chuyển mục đích sử dụng
			let change_purpose_price1 = (typeof asset.appraise_adapter[0] !== 'undefined') ? asset.appraise_adapter[0].change_purpose_price : null
			let change_purpose_price2 = (typeof asset.appraise_adapter[1] !== 'undefined') ? asset.appraise_adapter[1].change_purpose_price : null
			let change_purpose_price3 = (typeof asset.appraise_adapter[2] !== 'undefined') ? asset.appraise_adapter[2].change_purpose_price : null

			// tính chi phí chuyển đổi TSSS-1
			// this.price1 = 0
			// let price_facility_asset1 = 0
			// this.detail1.property_detail.forEach((item, index) => {
			//   this.form.asset_unit_price.forEach((itemPrice1, index2) => {
			//     if (this.baseAcronym === itemPrice1.land_type_data.acronym && (itemPrice1.asset_general_id === this.detail1.asset_general_id)) {
			//       if (itemPrice1.update === 2) {
			//         price_facility_asset1 = itemPrice1.update_value
			//       } else {
			//         price_facility_asset1 = itemPrice1.original_value
			//       }
			//     }
			//   })
			//   if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
			//     let circular_unit_price = item.circular_unit_price
			//     let appropriate_area = item.total_area
			//     if (typeof (this.form.asset_unit_price) !== 'undefined') {
			//       this.form.asset_unit_price.forEach((itemPrice1, index2) => {
			//         if ((itemPrice1.asset_general_id === this.detail1.asset_general_id) && (itemPrice1.land_type_id === item.land_type_purpose) && (itemPrice1.position_type_id === item.position_type_id)) {
			//           if (itemPrice1.update === 2) {
			//             circular_unit_price = itemPrice1.update_value
			//           } else {
			//             circular_unit_price = itemPrice1.original_value
			//           }
			//         }
			//       })
			//     }
			//     if (typeof (this.form.asset_unit_area) !== 'undefined') {
			//       this.form.asset_unit_area.forEach(itemArea1 => {
			//         if ((itemArea1.asset_general_id === this.detail1.asset_general_id) && (itemArea1.land_type_id === item.land_type_purpose) && (itemArea1.position_type_id === item.position_type_id)) {
			//           appropriate_area = item.total_area - itemArea1.violation_asset_area
			//         }
			//       })
			//     }
			//     let unitPrice = price_facility_asset1 - parseFloat(circular_unit_price)
			//     // this.price1 += unitPrice * parseFloat(appropriate_area)
			//     change_purpose_price1 += unitPrice * parseFloat(appropriate_area)
			//   }
			// })
			// // tính chi phí chuyển đổi TSSS-2
			// this.price2 = 0
			// let price_facility_asset2 = 0
			// this.detail2.property_detail.forEach((item, index) => {
			//   this.form.asset_unit_price.forEach((itemPrice2, index2) => {
			//     if (this.baseAcronym === itemPrice2.land_type_data.acronym && (itemPrice2.asset_general_id === this.detail2.asset_general_id)) {
			//       if (itemPrice2.update === 2) {
			//         price_facility_asset2 = itemPrice2.update_value
			//       } else {
			//         price_facility_asset2 = itemPrice2.original_value
			//       }
			//     }
			//   })
			//   if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
			//     let circular_unit_price = item.circular_unit_price
			//     let appropriate_area = item.total_area
			//     if (typeof (this.form.asset_unit_price) !== 'undefined') {
			//       this.form.asset_unit_price.forEach((itemPrice2, index2) => {
			//         if ((itemPrice2.asset_general_id === this.detail2.asset_general_id) && (itemPrice2.land_type_id === item.land_type_purpose) && (itemPrice2.position_type_id === item.position_type_id)) {
			//           if (itemPrice2.update === 2) {
			//             circular_unit_price = itemPrice2.update_value
			//           } else {
			//             circular_unit_price = itemPrice2.original_value
			//           }
			//         }
			//       })
			//     }
			//     if (typeof (this.form.asset_unit_area) !== 'undefined') {
			//       this.form.asset_unit_area.forEach(itemArea2 => {
			//         if ((itemArea2.asset_general_id === this.detail2.asset_general_id) && (itemArea2.land_type_id === item.land_type_purpose) && (itemArea2.position_type_id === item.position_type_id)) {
			//           appropriate_area = item.total_area - itemArea2.violation_asset_area
			//         }
			//       })
			//     }
			//     let unitPrice = price_facility_asset2 - parseFloat(circular_unit_price)
			//     // this.price2 += unitPrice * parseFloat(appropriate_area)
			//     change_purpose_price2 += unitPrice * parseFloat(appropriate_area)
			//   }
			// })
			// // tính chi phí chuyển đổi TSSS-3
			// this.price3 = 0
			// let price_facility_asset3 = 0
			// this.detail3.property_detail.forEach((item, index) => {
			//   this.form.asset_unit_price.forEach((itemPrice3, index3) => {
			//     if ((this.baseAcronym === itemPrice3.land_type_data.acronym) && (itemPrice3.asset_general_id === this.detail3.asset_general_id)) {
			//       if (itemPrice3.update === 2) {
			//         price_facility_asset3 = itemPrice3.update_value
			//       } else {
			//         price_facility_asset3 = itemPrice3.original_value
			//       }
			//     }
			//   })
			//   if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
			//     let circular_unit_price = item.circular_unit_price
			//     let appropriate_area = item.total_area
			//     if (typeof (this.form.asset_unit_price) !== 'undefined') {
			//       this.form.asset_unit_price.forEach((itemPrice3, index3) => {
			//         if ((itemPrice3.asset_general_id === this.detail3.asset_general_id) && (itemPrice3.land_type_id === item.land_type_purpose) && (itemPrice3.position_type_id === item.position_type_id)) {
			//           if (itemPrice3.update === 2) {
			//             circular_unit_price = itemPrice3.update_value
			//           } else {
			//             circular_unit_price = itemPrice3.original_value
			//           }
			//         }
			//       })
			//     }
			//     if (typeof (this.form.asset_unit_area) !== 'undefined') {
			//       this.form.asset_unit_area.forEach(itemArea3 => {
			//         if ((itemArea3.asset_general_id === this.detail3.asset_general_id) && (itemArea3.land_type_id === item.land_type_purpose) && (itemArea3.position_type_id === item.position_type_id)) {
			//           appropriate_area = item.total_area - itemArea3.violation_asset_area
			//         }
			//       })
			//     }
			//     let unitPrice = price_facility_asset3 - parseFloat(circular_unit_price)
			//     // this.price3 += unitPrice * parseFloat(appropriate_area)
			//     change_purpose_price3 += unitPrice * parseFloat(appropriate_area)
			//   }
			// })
			// tính Tổng giá trị tài sản ước tính
			this.totalPriceEstimate1 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent1 * asset1.total_amount) / 100 : 0)
			this.totalPriceEstimate2 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent2 * asset2.total_amount) / 100 : 0)
			this.totalPriceEstimate3 = ((typeof asset1.total_amount !== 'undefined') ? (asset_percent3 * asset3.total_amount) / 100 : 0)

			// tính giá trị Quyền sử dụng đất của TSSS
			// ------------------------------------------------------------------------- Tổng giá trị ước tính - Đơn giá vi phạm quy hoạch + Chi phí chuyển mục đích sử dụng - Đơn giá đơn vị xây dựng
			this.totalPrice1 = ((typeof asset1.total_estimate_amount !== 'undefined') ? (asset_percent1 * asset1.total_amount) / 100 : 0) - this.totalPriceViolationArea1 + +change_purpose_price1 - (this.dgxd1 * this.dtsxd1 * this.clcl1 / 100)
			this.totalPrice2 = ((typeof asset2.total_estimate_amount !== 'undefined') ? (asset_percent2 * asset2.total_amount) / 100 : 0) - this.totalPriceViolationArea2 + +change_purpose_price2 - (this.dgxd2 * this.dtsxd2 * this.clcl2 / 100)
			this.totalPrice3 = ((typeof asset3.total_estimate_amount !== 'undefined') ? (asset_percent3 * asset3.total_amount) / 100 : 0) - this.totalPriceViolationArea3 + +change_purpose_price3 - (this.dgxd3 * this.dtsxd3 * this.clcl3 / 100)

			// tính đơn giá dất của TSSS
			this.dgd1 = (typeof this.detail1.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice1 / this.arrayTotalAppropriateArea[this.detail1.asset_general_id]) : 0
			this.dgd2 = (typeof this.detail2.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice2 / this.arrayTotalAppropriateArea[this.detail2.asset_general_id]) : 0
			this.dgd3 = (typeof this.detail3.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice3 / this.arrayTotalAppropriateArea[this.detail3.asset_general_id]) : 0

			// this.dgd1 = (typeof this.detail1.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice1 / this.detail1.asset_general_land_sum_area) : 0
			// this.dgd2 = (typeof this.detail2.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice2 / this.detail2.asset_general_land_sum_area) : 0
			// this.dgd3 = (typeof this.detail3.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice3 / this.detail3.asset_general_land_sum_area) : 0

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
			if ((typeof comparisonFactor1['phap_ly'] !== 'undefined') && comparisonFactor1['phap_ly'].status === 1) {
				let percentPl1 = (typeof comparisonFactor1['phap_ly'].adjust_percent !== 'undefined') ? comparisonFactor1['phap_ly'].adjust_percent : 0
				let percentPl2 = (typeof comparisonFactor2['phap_ly'].adjust_percent !== 'undefined') ? comparisonFactor2['phap_ly'].adjust_percent : 0
				let percentPl3 = (typeof comparisonFactor3['phap_ly'].adjust_percent !== 'undefined') ? comparisonFactor3['phap_ly'].adjust_percent : 0
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
			let percentHdd1 = 0
			let percentHdd2 = 0
			let percentHdd3 = 0
			this.priceHdd1 = 0
			this.priceHdd2 = 0
			this.priceHdd3 = 0
			if ((typeof comparisonFactor1['hinh_dang_dat'] !== 'undefined') && comparisonFactor1['hinh_dang_dat'].status === 1) {
				percentHdd1 = (typeof comparisonFactor1['hinh_dang_dat'].adjust_percent !== 'undefined') ? comparisonFactor1['hinh_dang_dat'].adjust_percent : 0
				percentHdd2 = (typeof comparisonFactor2['hinh_dang_dat'].adjust_percent !== 'undefined') ? comparisonFactor2['hinh_dang_dat'].adjust_percent : 0
				percentHdd3 = (typeof comparisonFactor3['hinh_dang_dat'].adjust_percent !== 'undefined') ? comparisonFactor3['hinh_dang_dat'].adjust_percent : 0
				// mức điều chỉnh của yếu tố HÌNH DÁNG DẤT
				this.priceHdd1 = percentHdd1 * this.totalPricePL1 / 100
				this.priceHdd2 = percentHdd2 * this.totalPricePL2 / 100
				this.priceHdd3 = percentHdd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentHdd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentHdd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentHdd3 !== 0) ? 1 : 0
			}
			let percentKcd1 = 0
			let percentKcd2 = 0
			let percentKcd3 = 0
			this.priceKcd1 = 0
			this.priceKcd2 = 0
			this.priceKcd3 = 0
			if ((typeof comparisonFactor1['ket_cau_duong'] !== 'undefined') && comparisonFactor1['ket_cau_duong'].status === 1) {
				percentKcd1 = (typeof comparisonFactor1['ket_cau_duong'].adjust_percent !== 'undefined') ? comparisonFactor1['ket_cau_duong'].adjust_percent : 0
				percentKcd2 = (typeof comparisonFactor2['ket_cau_duong'].adjust_percent !== 'undefined') ? comparisonFactor2['ket_cau_duong'].adjust_percent : 0
				percentKcd3 = (typeof comparisonFactor3['ket_cau_duong'].adjust_percent !== 'undefined') ? comparisonFactor3['ket_cau_duong'].adjust_percent : 0
				// mức điều chỉnh của yếu tố KẾT CẤU ĐƯỜNG
				this.priceKcd1 = percentKcd1 * this.totalPricePL1 / 100 //
				this.priceKcd2 = percentKcd2 * this.totalPricePL2 / 100
				this.priceKcd3 = percentKcd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentKcd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentKcd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentKcd3 !== 0) ? 1 : 0
			}
			let percentKd1 = 0
			let percentKd2 = 0
			let percentKd3 = 0
			this.priceKd1 = 0
			this.priceKd2 = 0
			this.priceKd3 = 0
			if ((typeof comparisonFactor1['kinh_doanh'] !== 'undefined') && comparisonFactor1['kinh_doanh'].status === 1) {
				percentKd1 = (typeof comparisonFactor1['kinh_doanh'].adjust_percent !== 'undefined') ? comparisonFactor1['kinh_doanh'].adjust_percent : 0
				percentKd2 = (typeof comparisonFactor2['kinh_doanh'].adjust_percent !== 'undefined') ? comparisonFactor2['kinh_doanh'].adjust_percent : 0
				percentKd3 = (typeof comparisonFactor3['kinh_doanh'].adjust_percent !== 'undefined') ? comparisonFactor3['kinh_doanh'].adjust_percent : 0
				// mức điều chỉnh của yếu tố KINH DOANH
				this.priceKd1 = percentKd1 * this.totalPricePL1 / 100
				this.priceKd2 = percentKd2 * this.totalPricePL2 / 100
				this.priceKd3 = percentKd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentKd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentKd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentKd3 !== 0) ? 1 : 0
			}
			let percentDkht1 = 0
			let percentDkht2 = 0
			let percentDkht3 = 0
			this.priceDkht1 = 0
			this.priceDkht2 = 0
			this.priceDkht3 = 0
			if ((typeof comparisonFactor1['dieu_kien_ha_tang'] !== 'undefined') && comparisonFactor1['dieu_kien_ha_tang'].status === 1) {
				percentDkht1 = (typeof comparisonFactor1['dieu_kien_ha_tang'].adjust_percent !== 'undefined') ? comparisonFactor1['dieu_kien_ha_tang'].adjust_percent : 0
				percentDkht2 = (typeof comparisonFactor2['dieu_kien_ha_tang'].adjust_percent !== 'undefined') ? comparisonFactor2['dieu_kien_ha_tang'].adjust_percent : 0
				percentDkht3 = (typeof comparisonFactor3['dieu_kien_ha_tang'].adjust_percent !== 'undefined') ? comparisonFactor3['dieu_kien_ha_tang'].adjust_percent : 0
				// mức điều chỉnh của yếu tố ĐIỀU KIỆN HẠ TẦNG
				this.priceDkht1 = percentDkht1 * this.totalPricePL1 / 100
				this.priceDkht2 = percentDkht2 * this.totalPricePL2 / 100
				this.priceDkht3 = percentDkht3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentDkht1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDkht2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDkht3 !== 0) ? 1 : 0
			}
			let percentPt1 = 0
			let percentPt2 = 0
			let percentPt3 = 0
			this.pricePt1 = 0
			this.pricePt2 = 0
			this.pricePt3 = 0
			if ((typeof comparisonFactor1['phong_thuy'] !== 'undefined') && comparisonFactor1['phong_thuy'].status === 1) {
				percentPt1 = (typeof comparisonFactor1['phong_thuy'].adjust_percent !== 'undefined') ? comparisonFactor1['phong_thuy'].adjust_percent : 0
				percentPt2 = (typeof comparisonFactor2['phong_thuy'].adjust_percent !== 'undefined') ? comparisonFactor2['phong_thuy'].adjust_percent : 0
				percentPt3 = (typeof comparisonFactor3['phong_thuy'].adjust_percent !== 'undefined') ? comparisonFactor3['phong_thuy'].adjust_percent : 0
				// mức điều chỉnh của yếu tố PHONG THỦY
				this.pricePt1 = percentPt1 * this.totalPricePL1 / 100
				this.pricePt2 = percentPt2 * this.totalPricePL2 / 100
				this.pricePt3 = percentPt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentPt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentPt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentPt3 !== 0) ? 1 : 0
			}
			let percentDktt1 = 0
			let percentDktt2 = 0
			let percentDktt3 = 0
			this.priceDktt1 = 0
			this.priceDktt2 = 0
			this.priceDktt3 = 0
			if ((typeof comparisonFactor1['dieu_kien_thanh_toan'] !== 'undefined') && comparisonFactor1['dieu_kien_thanh_toan'].status === 1) {
				percentDktt1 = (typeof comparisonFactor1['dieu_kien_thanh_toan'].adjust_percent !== 'undefined') ? comparisonFactor1['dieu_kien_thanh_toan'].adjust_percent : 0
				percentDktt2 = (typeof comparisonFactor2['dieu_kien_thanh_toan'].adjust_percent !== 'undefined') ? comparisonFactor2['dieu_kien_thanh_toan'].adjust_percent : 0
				percentDktt3 = (typeof comparisonFactor3['dieu_kien_thanh_toan'].adjust_percent !== 'undefined') ? comparisonFactor3['dieu_kien_thanh_toan'].adjust_percent : 0
				// mức điều chỉnh của yếu tố ĐIỀU KIỆN THANH TOÁN
				this.priceDktt1 = percentDktt1 * this.totalPricePL1 / 100
				this.priceDktt2 = percentDktt2 * this.totalPricePL2 / 100
				this.priceDktt3 = percentDktt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentDktt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDktt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDktt3 !== 0) ? 1 : 0
			}
			let percentQm1 = 0
			let percentQm2 = 0
			let percentQm3 = 0
			this.priceQm1 = 0
			this.priceQm2 = 0
			this.priceQm3 = 0
			if ((typeof comparisonFactor1['quy_mo'] !== 'undefined') && comparisonFactor1['quy_mo'].status === 1) {
				percentQm1 = (typeof comparisonFactor1['quy_mo'].adjust_percent !== 'undefined') ? comparisonFactor1['quy_mo'].adjust_percent : 0
				percentQm2 = (typeof comparisonFactor2['quy_mo'].adjust_percent !== 'undefined') ? comparisonFactor2['quy_mo'].adjust_percent : 0
				percentQm3 = (typeof comparisonFactor3['quy_mo'].adjust_percent !== 'undefined') ? comparisonFactor3['quy_mo'].adjust_percent : 0
				// mức điều chỉnh của yếu tố QUY MÔ
				this.priceQm1 = percentQm1 * this.totalPricePL1 / 100
				this.priceQm2 = percentQm2 * this.totalPricePL2 / 100
				this.priceQm3 = percentQm3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentQm1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentQm2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentQm3 !== 0) ? 1 : 0
			}
			let percentCrmt1 = 0
			let percentCrmt2 = 0
			let percentCrmt3 = 0
			this.priceCrmt1 = 0
			this.priceCrmt2 = 0
			this.priceCrmt3 = 0
			if ((typeof comparisonFactor1['chieu_rong_mat_tien'] !== 'undefined') && comparisonFactor1['chieu_rong_mat_tien'].status === 1) {
				percentCrmt1 = (typeof comparisonFactor1['chieu_rong_mat_tien'].adjust_percent !== 'undefined') ? comparisonFactor1['chieu_rong_mat_tien'].adjust_percent : 0
				percentCrmt2 = (typeof comparisonFactor2['chieu_rong_mat_tien'].adjust_percent !== 'undefined') ? comparisonFactor2['chieu_rong_mat_tien'].adjust_percent : 0
				percentCrmt3 = (typeof comparisonFactor3['chieu_rong_mat_tien'].adjust_percent !== 'undefined') ? comparisonFactor3['chieu_rong_mat_tien'].adjust_percent : 0
				// mức điều chỉnh của yếu tố CHIỀU RỘNG MẶT TIỀN
				this.priceCrmt1 = percentCrmt1 * this.totalPricePL1 / 100
				this.priceCrmt2 = percentCrmt2 * this.totalPricePL2 / 100
				this.priceCrmt3 = percentCrmt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentCrmt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentCrmt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentCrmt3 !== 0) ? 1 : 0
			}
			let percentCskd1 = 0
			let percentCskd2 = 0
			let percentCskd3 = 0
			this.priceCskd1 = 0
			this.priceCskd2 = 0
			this.priceCskd3 = 0
			if ((typeof comparisonFactor1['chieu_sau_khu_dat'] !== 'undefined') && comparisonFactor1['chieu_sau_khu_dat'].status === 1) {
				percentCskd1 = (typeof comparisonFactor1['chieu_sau_khu_dat'].adjust_percent !== 'undefined') ? comparisonFactor1['chieu_sau_khu_dat'].adjust_percent : 0
				percentCskd2 = (typeof comparisonFactor2['chieu_sau_khu_dat'].adjust_percent !== 'undefined') ? comparisonFactor2['chieu_sau_khu_dat'].adjust_percent : 0
				percentCskd3 = (typeof comparisonFactor3['chieu_sau_khu_dat'].adjust_percent !== 'undefined') ? comparisonFactor3['chieu_sau_khu_dat'].adjust_percent : 0
				// mức điều chỉnh của yếu tố CHIỀU SÂU KHU ĐẤT
				this.priceCskd1 = percentCskd1 * this.totalPricePL1 / 100
				this.priceCskd2 = percentCskd2 * this.totalPricePL2 / 100
				this.priceCskd3 = percentCskd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentCskd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentCskd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentCskd3 !== 0) ? 1 : 0
			}
			let percentDrd1 = 0
			let percentDrd2 = 0
			let percentDrd3 = 0
			this.priceDrd1 = 0
			this.priceDrd2 = 0
			this.priceDrd3 = 0
			if ((typeof comparisonFactor1['do_rong_duong'] !== 'undefined') && comparisonFactor1['do_rong_duong'].status === 1) {
				percentDrd1 = (typeof comparisonFactor1['do_rong_duong'].adjust_percent !== 'undefined') ? comparisonFactor1['do_rong_duong'].adjust_percent : 0
				percentDrd2 = (typeof comparisonFactor2['do_rong_duong'].adjust_percent !== 'undefined') ? comparisonFactor2['do_rong_duong'].adjust_percent : 0
				percentDrd3 = (typeof comparisonFactor3['do_rong_duong'].adjust_percent !== 'undefined') ? comparisonFactor3['do_rong_duong'].adjust_percent : 0
				// mức điều chỉnh của yếu tố ĐỘ RỘNG ĐƯỜNG
				this.priceDrd1 = percentDrd1 * this.totalPricePL1 / 100
				this.priceDrd2 = percentDrd2 * this.totalPricePL2 / 100
				this.priceDrd3 = percentDrd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentDrd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDrd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDrd3 !== 0) ? 1 : 0
			}
			let percentGt1 = 0
			let percentGt2 = 0
			let percentGt3 = 0
			this.priceGt1 = 0
			this.priceGt2 = 0
			this.priceGt3 = 0
			if ((typeof comparisonFactor1['giao_thong'] !== 'undefined') && comparisonFactor1['giao_thong'].status === 1) {
				percentGt1 = (typeof comparisonFactor1['giao_thong'].adjust_percent !== 'undefined') ? comparisonFactor1['giao_thong'].adjust_percent : 0
				percentGt2 = (typeof comparisonFactor2['giao_thong'].adjust_percent !== 'undefined') ? comparisonFactor2['giao_thong'].adjust_percent : 0
				percentGt3 = (typeof comparisonFactor3['giao_thong'].adjust_percent !== 'undefined') ? comparisonFactor3['giao_thong'].adjust_percent : 0
				// mức điều chỉnh của yếu tố GIAO THÔNG
				this.priceGt1 = percentGt1 * this.totalPricePL1 / 100
				this.priceGt2 = percentGt2 * this.totalPricePL2 / 100
				this.priceGt3 = percentGt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentGt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentGt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentGt3 !== 0) ? 1 : 0
			}
			let percentAnmts1 = 0
			let percentAnmts2 = 0
			let percentAnmts3 = 0
			this.priceAnmts1 = 0
			this.priceAnmts2 = 0
			this.priceAnmts3 = 0
			if ((typeof comparisonFactor1['an_ninh_moi_truong_song'] !== 'undefined') && comparisonFactor1['an_ninh_moi_truong_song'].status === 1) {
				percentAnmts1 = (typeof comparisonFactor1['an_ninh_moi_truong_song'].adjust_percent !== 'undefined') ? comparisonFactor1['an_ninh_moi_truong_song'].adjust_percent : 0
				percentAnmts2 = (typeof comparisonFactor2['an_ninh_moi_truong_song'].adjust_percent !== 'undefined') ? comparisonFactor2['an_ninh_moi_truong_song'].adjust_percent : 0
				percentAnmts3 = (typeof comparisonFactor3['an_ninh_moi_truong_song'].adjust_percent !== 'undefined') ? comparisonFactor3['an_ninh_moi_truong_song'].adjust_percent : 0
				// mức điều chỉnh của yếu tố AN NINH, MÔI TRƯỜNG SỐNG
				this.priceAnmts1 = percentAnmts1 * this.totalPricePL1 / 100
				this.priceAnmts2 = percentAnmts2 * this.totalPricePL2 / 100
				this.priceAnmts3 = percentAnmts3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentAnmts1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentAnmts2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentAnmts3 !== 0) ? 1 : 0
			}
			let percentQh1 = 0
			let percentQh2 = 0
			let percentQh3 = 0
			this.priceQh1 = 0
			this.priceQh2 = 0
			this.priceQh3 = 0
			if ((typeof comparisonFactor1['quy_hoach'] !== 'undefined') && comparisonFactor1['quy_hoach'].status === 1) {
				percentQh1 = (typeof comparisonFactor1['quy_hoach'].adjust_percent !== 'undefined') ? comparisonFactor1['quy_hoach'].adjust_percent : 0
				percentQh2 = (typeof comparisonFactor2['quy_hoach'].adjust_percent !== 'undefined') ? comparisonFactor2['quy_hoach'].adjust_percent : 0
				percentQh3 = (typeof comparisonFactor3['quy_hoach'].adjust_percent !== 'undefined') ? comparisonFactor3['quy_hoach'].adjust_percent : 0
				// mức điều chỉnh của yếu tố AN NINH, MÔI TRƯỜNG SỐNG
				this.priceQh1 = percentQh1 * this.totalPricePL1 / 100
				this.priceQh2 = percentQh2 * this.totalPricePL2 / 100
				this.priceQh3 = percentQh3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentQh1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentQh2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentQh3 !== 0) ? 1 : 0
			}
			let percentYtk1 = 0
			let percentYtk2 = 0
			let percentYtk3 = 0
			this.priceYtk1 = 0
			this.priceYtk2 = 0
			this.priceYtk3 = 0
			if ((typeof comparisonFactor1['yeu_to_khac'] !== 'undefined') && comparisonFactor2['yeu_to_khac'] && comparisonFactor3['yeu_to_khac'] && comparisonFactor1['yeu_to_khac'].status === 1) {
				percentYtk1 = (typeof comparisonFactor1['yeu_to_khac'].adjust_percent !== 'undefined') ? comparisonFactor1['yeu_to_khac'].adjust_percent : 0
				percentYtk2 = (typeof comparisonFactor2['yeu_to_khac'].adjust_percent !== 'undefined') ? comparisonFactor2['yeu_to_khac'].adjust_percent : 0
				percentYtk3 = (typeof comparisonFactor3['yeu_to_khac'].adjust_percent !== 'undefined') ? comparisonFactor3['yeu_to_khac'].adjust_percent : 0
				// mức điều chỉnh của yếu tố YẾU TỐ KHÁC
				this.priceYtk2 = percentYtk2 * this.totalPricePL2 / 100
				this.priceYtk3 = percentYtk3 * this.totalPricePL3 / 100
				this.priceYtk1 = percentYtk1 * this.totalPricePL1 / 100

				this.comparisonFactorChange1 += (percentYtk1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentYtk2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentYtk3 !== 0) ? 1 : 0
			}

			// Yếu khác thêm động
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
							arrayTem.push({
								index: item.position,
								indication_price_asset: dataOther.adjust_percent * arrayPriceTem[indexAsset] / 100
							})
						})
						this.price_other_comparison.push(arrayTem)
						arrayTem = []
					}
				})
			})
			asset.asset_general.forEach((asset, index) => {
				const asset_comparison_data = this.other_comparison.filter(data => data.asset_general_id === asset.id)
				mgcd_price_other[index] = 0
				asset_comparison_data.forEach((dataOther) => {
					mgcd_price_other[index] += dataOther.adjust_percent * arrayPriceTem[index] / 100
				})
			})
			// tính mức giá chỉ dẫn của TSSS
			this.mgcd1 = this.totalPricePL1 + this.priceHdd1 + this.priceKcd1 + this.priceKd1 + this.priceDkht1 + this.pricePt1 + this.priceDktt1 + this.priceQm1 + this.priceCrmt1 + this.priceCskd1 + this.priceDrd1 + this.priceGt1 + this.priceAnmts1 + this.priceQh1 + mgcd_price_other[0]
			this.mgcd2 = this.totalPricePL2 + this.priceHdd2 + this.priceKcd2 + this.priceKd2 + this.priceDkht2 + this.pricePt2 + this.priceDktt2 + this.priceQm2 + this.priceCrmt2 + this.priceCskd2 + this.priceDrd2 + this.priceGt2 + this.priceAnmts2 + this.priceQh2 + mgcd_price_other[1]
			this.mgcd3 = this.totalPricePL3 + this.priceHdd3 + this.priceKcd3 + this.priceKd3 + this.priceDkht3 + this.pricePt3 + this.priceDktt3 + this.priceQm3 + this.priceCrmt3 + this.priceCskd3 + this.priceDrd3 + this.priceGt3 + this.priceAnmts3 + this.priceQh3 + mgcd_price_other[2]

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

			// tổng giá trị điều chỉnh thuần
			this.tldc1 = this.priceHdd1 + this.priceKcd1 + this.priceKd1 + this.priceDkht1 + this.pricePt1 + this.priceDktt1 + this.priceQm1 + this.priceCrmt1 + this.priceCskd1 + this.priceDrd1 + mgcd_price_other[0]
			this.tldc2 = this.priceHdd2 + this.priceKcd2 + this.priceKd2 + this.priceDkht2 + this.pricePt2 + this.priceDktt2 + this.priceQm2 + this.priceCrmt2 + this.priceCskd2 + this.priceDrd2 + mgcd_price_other[1]
			this.tldc3 = this.priceHdd3 + this.priceKcd3 + this.priceKd3 + this.priceDkht3 + this.pricePt3 + this.priceDktt3 + this.priceQm3 + this.priceCrmt3 + this.priceCskd3 + this.priceDrd3 + mgcd_price_other[2]
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
			// ------------------------------ tính gía đất vi pham quy hoạch ----------------------------------- //
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
		},
		calculationChangePrice (asset) {
			// thông tin TSSS
			let asset1 = (typeof asset.asset_general[0] !== 'undefined') ? asset.asset_general[0] : null
			let asset2 = (typeof asset.asset_general[1] !== 'undefined') ? asset.asset_general[1] : null
			let asset3 = (typeof asset.asset_general[2] !== 'undefined') ? asset.asset_general[2] : null

			// lấy diện tích sàn xây dựng
			this.dtsxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].total_construction_base : 0
			this.dtsxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].total_construction_base : 0
			this.dtsxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].total_construction_base : 0

			// khai báo biến Chi phí chuyển mục đích sử dụng
			let purpose_price1 = 0
			let purpose_price2 = 0
			let purpose_price3 = 0

			// lấy property của TSSS
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
			// --------------------------------Tính diện tích đất TSSS (Quy mô) -----------------------------------------//
			// Tính tổng diện tích đất vi phạm quy hoạch
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
				// Tính tổng diện tích đất phù hợp quy hoạch

				// Tính diện tích đất phù hợp quy hoạch khi thay đổi diện tích vi phạm quy hoạch
			})
			// xóa dữ liệu cũ
			this.asset_appropriate_area_arr = []
			// quét update lại asset_appropriate_area_arr
			this.form.asset_general.forEach(asset => {
				asset.properties.forEach(property_asset => {
					let appraiseHasAsset = this.form.appraise_has_assets.filter(item => item.asset_property_detail_id === property_asset.id)
					if (appraiseHasAsset && appraiseHasAsset.length > 0) {
						// lấy giá trị tổng diện tích đất phù hợp
						this.arrayTotalAppropriateArea[asset.id] = property_asset.asset_general_land_sum_area - this.arrayTotalViolationArea[asset.id]
						property_asset.property_detail.forEach(property_asset_detail => {
							// lấy giá trị đất vi phạm quy hoạch từ asset_unit_area
							let getDataUnitPrice = this.form.asset_unit_area.filter(itemVioLand => itemVioLand.asset_general_id === property_asset.asset_general_id && itemVioLand.land_type_id === property_asset_detail.land_type_purpose)
							// push diện tích đất phù hợp quy hoạch
							if (getDataUnitPrice && getDataUnitPrice.length > 0) {
								this.asset_appropriate_area_arr.push({
									asset_general_id: property_asset.asset_general_id,
									total_area: property_asset_detail.total_area - +getDataUnitPrice[0].violation_asset_area,
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
					// check tồn tại trong đất phù hợp quy hoạch
					let checkExist = this.appropriate_zoning_land.filter(checkItem => checkItem.name_purpose_land === itemLandAsset.name_purpose_land_asset)
					if (checkExist && checkExist.length > 0) {
						// thêm mục đất của TSSS vào mục đích đất đã tồn tại
						this.appropriate_zoning_land.forEach((appropriateLand, index) => {
							if (appropriateLand.name_purpose_land === itemLandAsset.name_purpose_land_asset) {
								this.appropriate_zoning_land[index]['asset_general_land'] = dataLand
							}
						})
					} else {
						// thêm mục đất của TSSS có mà TSTD k có
						this.appropriate_zoning_land.push({
							name_purpose_land: itemLandAsset.name_purpose_land_asset,
							appropriate_appraise_land: 0,
							asset_general_land: dataLand
						})
					}
				}
			})

			// tính Đơn giá xây dựng của TSSS
			this.dgxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].unit_price_m2 : 0
			this.dgxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].unit_price_m2 : 0
			this.dgxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].unit_price_m2 : 0

			// lấy tỉ lệ Chất lượng còn lại của TSSS
			this.clcl1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].remaining_quality : 0
			this.clcl2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].remaining_quality : 0
			this.clcl3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].remaining_quality : 0

			// lấy giá cơ sở
			this.baseUnitPrice = 0 // giá đất cơ sở của UBND
			this.baseAcronym = '' // name tắt của loại đất
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

			// --------------------------- Tính đơn giá diện tích vi phạm quy hoạch ---------------------------//
			// đơn giá diện tích vi phạm quy hoạch TTSS-1
			this.totalPriceViolationArea1 = 0
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
					this.totalPriceViolationArea1 = price_violation1.reduce((a, b) => a + b, 0)
					price_violation1 = []
				} else if (price_violation1.length > 0 && price_violation1.length === 1) {
					this.totalPriceViolationArea1 = price_violation1[0]
					price_violation1 = []
				}
			})
			// đơn giá diện tích vi phạm quy hoạch TTSS-2
			this.totalPriceViolationArea2 = 0
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
					this.totalPriceViolationArea2 = price_violation2.reduce((a, b) => a + b, 0)
					price_violation2 = []
				} else if (price_violation2.length > 0 && price_violation2.length === 1) {
					this.totalPriceViolationArea2 = price_violation2[0]
					price_violation2 = []
				}
			})
			// đơn giá diện tích vi phạm quy hoạch TTSS-3
			this.totalPriceViolationArea3 = 0
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
					this.totalPriceViolationArea3 = price_violation3.reduce((a, b) => a + b, 0)
					price_violation3 = []
				} else if (price_violation3.length > 0 && price_violation3.length === 1) {
					this.totalPriceViolationArea3 = price_violation3[0]
					price_violation3 = []
				}
			})
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
			this.form.appraise_adapter[0].change_purpose_price = purpose_price1
			this.form.appraise_adapter[1].change_purpose_price = purpose_price2
			this.form.appraise_adapter[2].change_purpose_price = purpose_price3
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
		async handleAddOtherFactor () {
			const form = this.form
			this.data_other_comparison = []
			let indexTem = {}
			this.otherIndex++
			const map = new Map()
			await this.form.asset_general.forEach(asset => {
				// tạo data gửi
				this.other_comparison.push({
					adjust_percent: 0,
					appraise_id: form.id,
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
			this.$emit('changeInput', this.form)
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
		getOtherFactor () {
			if (typeof this.formData !== 'undefined') {
				this.formData.comparison_factor.forEach(data => {
					if (data.type === 'yeu_to_khac') {
						this.showOtherFactor = true
						if (data.name) {
							this.otherFactor = data.name
						} else {
							this.otherFactor = 'Yếu tố khác'
						}
					}
				})
			}
		},
		getData () {
			if ((typeof this.form !== 'undefined') && (this.form !== null)) {
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
				if (this.form.properties[0].property_detail && this.form.properties[0].property_detail.length > 1) {
					this.type_is_not_facility = this.form.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === false).land_type_purpose.acronym
				}
				// set config render data
				this.appraises = {}
				let appraise = JSON.parse(JSON.stringify(this.formData))
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
						propertyChoosing: arrayPropertyChoosing,
						comparison_factor: asset_generals,
						asset_general: appraise.asset_general,
						asset_type: appraise.asset_type,
						appraise_has_assets: appraise.appraise_has_assets,
						properties: appraise.properties,
						construction_company: appraise.construction_company,
						arrayCPMDSD: appraise.cpcmdsd,
						arrayDGBQ: appraise.dgbq,
						tangible_assets: appraise.tangible_assets,
						full_address_appraise: full_address_appraise
					}
				}
			}
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
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
		handleCancel (id) {
			this.$emit('cancel', id)
		},
		saveData () {
			const dataSave = []
			const otherDataSave = this.other_comparison
			const dataDelete = this.delete_other_comparison
			const asset_unit_price = this.form.asset_unit_price
			const asset_unit_area = this.form.asset_unit_area
			const round_total = this.round_total
			const round_composite = this.round_composite
			const round_violation_composite = this.round_violation_composite
			const round_violation_facility = this.round_violation_facility
			const appraise_adapter = this.form.appraise_adapter
			const layer_cutting_procedure = this.layer_cutting_procedure
			const layer_cutting_procedure_price = this.layer_cutting_procedure_price
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
				remaining_price: remaining_price

			}
			if (checkArea) {
				this.showAreaLand = true
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = false
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch phải lớn hơn 0',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkTypeArea) {
				this.showAreaLand = true
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = false
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (checkPrice) {
				this.showAreaLand = false
				this.showPriceCommitee = true
				this.showDetailAppraise = false
				this.showDetailComparison = false
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Đơn giá UBND không được âm',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (this.showError) {
				this.showAreaLand = false
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = true
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Mức giá chỉ dẫn đang âm , vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (layer_cutting_procedure && !layer_cutting_procedure_price) {
				this.showAreaLand = false
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = true
				this.showResultAppraise = false
				this.$toast.open({
					message: 'vui lòng nhập giá trị phương pháp cắt lớp',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_total < -7 || round_total > 7) {
				this.showAreaLand = false
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = true
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_composite < -7 || round_composite > 7) {
				this.showAreaLand = false
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = true
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_violation_composite < -7 || round_violation_composite > 7) {
				this.showAreaLand = false
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = true
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else if (round_violation_facility < -7 || round_violation_facility > 7) {
				this.showAreaLand = false
				this.showPriceCommitee = false
				this.showDetailAppraise = false
				this.showDetailComparison = true
				this.showResultAppraise = false
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else this.postData(payloadData)
			// else this.postData(dataSave, otherDataSave, dataDelete, price_UBND, round_total, appraise_adapter, remaining_price, layer_cutting_procedure_price, layer_cutting_procedure)
		},
		async postData (payloadData) {
			const res = await Certificate.postData(payloadData)
			if (res.data) {
				this.$toast.open({
					message: 'Điều chỉnh phụ lục 1 thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$emit('cancel')
				this.$emit('action', this.formData.id)
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		changeLegalRate (e, indexAsset, index, type) {
			let tempArrayComparison = []
			if (e) {
				this.appraises.comparison_factor[indexAsset].comparison_factor[index].adjust_percent = e
			} else {
				this.appraises.comparison_factor[indexAsset].comparison_factor[index].adjust_percent = 0
			}
			this.appraises.comparison_factor.forEach(data => {
				data.comparison_factor.forEach(item => {
					tempArrayComparison.push(item)
				})
			})
			this.form.comparison_factor = tempArrayComparison
			this.calculation(this.form)
		}
	}
}
</script>

<style scoped lang="scss">
  .modal-detail {
    position: fixed;
    z-index: 1031;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.6);
    @media (max-width: 768px) {
      padding: 20px;
    }
  .card {
      ::-webkit-scrollbar {
        width: 12px !important;
      }
      border-radius: 5px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
      max-width: 1400px;
      width: 100%;
      max-height: 90vh;
      margin-bottom: 0;
      padding: 20px 30px;
      @media (max-width: 768px) {
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
  .title-property{
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 18px;
  }
  .input-contain{
    margin-bottom: 25px;
  }
  .card-table{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    max-width: 99%;
    margin: 50px auto 75px;
  }
  .card-table tbody tr:last-child td, .card-table tbody tr:last-child th{
    border-bottom: 1px solid #E5E5E5 ;
  }
  .card{
    .contain-detail{
      overflow-y: scroll;
      overflow-x: hidden;
      margin-top: 20px;
      margin-bottom: 20px;
      &::-webkit-scrollbar {
        width: 2px;
      }
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    // &-body{
    //   padding: 35px 30px 40px;
    // }
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
  .table-property{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px;
        font-weight: 500;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
      }
    }
  }
  .table_price_committee {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 24%;
          border-left: none;
        }
        &:nth-child(2) {
          width: 19%;
        }
        &:nth-child(3) {
          width: 19%;
        }
        &:nth-child(4) {
          width: 19%;
        }
        &:nth-child(5) {
          width: 19%;
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
      }
    }
  }
  .table_detail_property {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 5%;
          border-left: none;
          min-width: 64.5px;
        }
        &:nth-child(2) {
          width: 19%;
          min-width: 215px;
        }
        &:nth-child(3) {
          width: 19%;
        }
        &:nth-child(4) {
          width: 19%;
        }
        &:nth-child(5) {
          width: 19%;
        }
        &:nth-child(6) {
          width: 19%;
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
      }
    }
  }
  .table-comparision{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
        // padding: 12px;
        // font-weight: 500;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 5%;
          border-left: none;
          min-width: 64.5px;
        }
        &:nth-child(2) {
          width: 19%;
          min-width: 215px;
        }
        &:nth-child(3) {
          width: 19%;
          min-width: 229px;
        }
        &:nth-child(4) {
          width: 19%;
          min-width: 229px;
        }
        &:nth-child(5) {
          width: 19%;
          min-width: 229px;
        }
        &:nth-child(6) {
          width: 19%;
          min-width: 229px;
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
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
    min-width: 229px;
  }
  .td_none {
    padding: unset !important;
    border: unset !important
  }
  .btn-delete{
    cursor: pointer;
    display: flex;
    align-items: center;
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
  .table_result_price {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 21.5%;
          border-left: none;
        }
        &:nth-child(2) {
          width: 19%;
        }
        &:nth-child(3) {
          width: 19%;
        }
        &:nth-child(4) {
          width: 19%;
        }
        &:nth-child(5) {
          width: 19%;
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
      }
    }
  }
  .contain-table{
    overflow-x: auto;
    @media (max-width: 1024px) {
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
  .btn-property{
    padding: 10px;
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .img-upload{
    margin-left: 20px;
    position: relative;
    width: 123px;
    height: 35px;
    color: #fff;
    background: #FAA831;

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
    height: auto;
    position: relative;
    .img{
      width: 100%;
    }
    .delete{
      position: absolute;
      top: 0;
      right: 0;
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
  .contain-total{
    &__left{
      color: #000000;
      .num{
        padding: 0 11px 0 24px;
        width: 340px;
        height: 35px;
        line-height: 1.5;
        border-radius: 5px;
        border: 1px solid #555555;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        background: #f1f1f1 !important;
        cursor: not-allowed;
        user-select: none;
        p{
          margin-bottom: 0;
        }
      }
      .name{
        min-width: 175px;
        margin-bottom: 0;
        margin-right: 20px;
      }
    }
  }
  .img-locate{
    cursor: pointer;
    position: absolute;
    right: 15px;
    top: 35px;
  }
  .form-control {
    width: 100%;
  }
  .btn-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 146px;
    color: #fff;
    margin-right: 15px;
    box-sizing: border-box;
    &:hover{
      border-color: #dc8300;
    }
  }
  .btn-orange-other {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 155px;
    color: #fff;
    margin-right: 15px;
    box-sizing: border-box;
    &:hover{
      border-color: #dc8300;
    }
  }
  .container-title{
    margin: -20px -30px auto;
    padding: 25px 30px 0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    @media (max-width: 767px) {
      margin: -20px -10px auto;
      padding: 20px 10px 0;
    }
    .title{
      font-size: 1.2rem;
      margin-bottom: 25px;
      font-weight: 700;
      @media (max-width: 767px) {
        font-size: 1.125rem;
      }
    }
    &__footer{
      margin: auto -30px -20px;
      padding: 20px 30px 20px;
      @media (max-width: 767px) {
        margin: auto -10px -20px;
        padding: 20px 10px 0;
        .btn-white{
          margin-bottom: 20px;
        }
      }
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
      margin-right: 0 ;
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
  .container-img{
    padding: .75rem 0;
    border: 1px solid #0b0d10;
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
  .input-disabled {
    min-height: 30px;
    height: 33px;
  }
  .text-none{
    text-transform: none;
  }
  .table-property {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      tr {
        border-radius: 0 5px 5px 0;
      }
      th {
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
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
    &_other_factor {
      td{
        border: 1px solid #E5E5E5;
        padding: 20px 14px;
      }
    }
  }
  .container-table {
    border-radius: 5px;
    border: 1px solid #F3F2F7;
  }
  ::-webkit-scrollbar {
  -webkit-appearance: none;
  width: 10px;
  }

  ::-webkit-scrollbar-thumb {
    border-radius: 20px;
    background-color: rgba(0, 0, 0, .5);
    box-shadow: 0 0 1px rgba(255, 255, 255, .5);
  }
  .inputLabelCompare {
    text-align: center;
    font-weight: 700;
  }

  .percent {
    margin-left: 10px;
    margin-top: 5px;
    font-size: 1.125rem;
  }
  .container_round {
    padding-left: 40%;
    height: 30px;
  }
  .title_round {
    margin-right:20px;
    padding-top:5px
  }
  .marginTop {
    margin-top: 0.5rem !important;
  }

</style>
