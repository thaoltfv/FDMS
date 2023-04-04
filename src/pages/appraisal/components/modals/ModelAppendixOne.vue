<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer">
          <div class="contain-detail">
            <h3 v-if="appraises.asset_general && appraises.asset_general.length > 0">BẢNG CHỈNH SỬA ĐƠN GIÁ UBND TSTĐG VÀ TSSS</h3>
            <div class="container-table" v-if="appraises.asset_general && appraises.asset_general.length > 0">
                  <table class="table-property">
                    <thead>
                    <tr>
                      <th>Đơn giá theo QĐ</th>
                      <th>TSTĐ</th>
                      <th v-for="(asset, index) in appraises.asset_general" :key="'header' + index" >{{ 'TSSS ' + asset.id }}</th>
                    </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(data, index) in data_land_price" :key="'body-price' + index" >
                        <td>{{data.name_purpose_land + (data.facility ? ' (Đất cơ sở)' : '')}}</td>
                        <td>
                          <div v-if="data.price_appraise">
                            {{format(data.price_appraise) + 'đ'}}
                          </div>
                          <div v-else>-</div>
                        </td>
                        <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">
                          <div v-if="!checkDataPriceUBND(asset, data.price_purpose_land)">
                            <div v-for="(price_data, index) in data.price_purpose_land" :key="'pricetype' + index" v-if="price_data.asset_general_id === asset.id">
                             <InputNumberFormat
                                class="label-none"
                                v-model="price_data.update_value"
                                vid="price_asset"
                                label="price"
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

              <h3 class="mt-5" v-if="appraises.asset_general && appraises.asset_general.length > 0">BẢNG THÔNG TIN TSTĐG VÀ TSSS</h3>
                <div class="container-table" v-if="appraises.asset_general && appraises.asset_general.length > 0">
                  <table class="table-property">
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
                      <td></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ asset.asset_type.description }}</td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>Thời điểm giao dịch</td>
                      <td></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetType' + index">{{ asset.transaction_type.description }}</td>
                    </tr>
                    <tr>
                      <td>12</td>
                      <td>Đơn giá xây dựng mới</td>
                      <td>{{  indicativePrice[0] && (typeof indicativePrice[0].unit_price_m2 !== 'undefined') ? format(indicativePrice[0].unit_price_m2) + 'đ' : '-' }}</td>
                      <td>{{format(dgxd1)}}đ</td>
                      <td>{{format(dgxd1)}}đ</td>
                      <td>{{format(dgxd1)}}đ</td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetConstructionAmount' + index">{{asset.tangible_assets && asset.tangible_assets.length > 0 ? format(asset.tangible_assets[0].unit_price_m2) + 'đ' : '-' }}</td> -->
                    </tr>
                    <tr>
                      <td>13</td>
                      <td>Vị trí</td>
                      <td>{{ (typeof appraises.properties !== 'undefined') && appraises.properties.length > 0 ? appraises.properties[0].description : '' }}</td>
                      <td v-for="(asset, indexAsset) in appraises.asset_general" :key="'assetProperty' + indexAsset">
                        <!-- <span v-for="(property, index) in asset.properties" v-if="property.id === appraises.appraise_has_assets[indexAsset].asset_property_detail_id" :key="'property' +index">{{property.description}}</span> -->
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
                      <td>Lợi thế kinh doanh</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'appraisalBussiness' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetBussiness' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>16</td>
                      <td>Cơ sở hạ tầng</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'appraisalBasis' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetBasis' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>17</td>
                      <td>An ninh, môi trường sống</td>
                     <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'appraisalSecurity' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>18</td>
                      <td>
                        Phong thủy
                      </td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'appraisalSecurity' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>19</td>
                      <td>Điều kiện thanh toán</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'appraisalPay' + index">{{comparison_factor.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetPay' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +index">{{comparison_factor.asset_title}}</span></td>
                    </tr>
                    <tr>
                      <td>20</td>
                      <td>Độ rộng đường</td>
                      <td v-for="(comparison_factor, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'appraisalStreet' + index">{{comparison_factor.appraise_title}}m</td>
                      <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +index">{{comparison_factor.asset_title}}m</span></td>
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
                      <td v-for="(asset, index) in appraises.asset_general" :key="'adjustPercent' + index">{{ 100 + parseInt(asset.adjust_percent) }} %</td>
                    </tr>
                    <tr>
                      <td>23</td>
                      <td>Tổng giá trị tài sản ước tính</td>
                      <td></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'adjustPercent' + index">{{format(asset.total_amount * (1 + asset.adjust_percent / 100))}}đ</td>
                    </tr>
                    <tr>
                      <td>25</td>
                      <td>Chi phí chuyển mục đích sử dụng</td>
                      <td></td>
                      <td>{{format(price1)}}</td>
                      <td>{{format(price2)}}</td>
                      <td>{{format(price3)}}</td>
                    </tr>
                    <tr>
                      <td>26</td>
                      <!-- <td>Giá trị QSDĐ {{appraises.properties[0] && appraises.properties[0].property_detail ? appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym : ''}} ước tính</td> -->
                     <td>Giá trị QSDĐ {{showTypeLand(appraises)}} ước tính</td>
                      <td></td>
                      <td>{{format(totalPrice1)}}</td>
                      <td>{{format(totalPrice2)}}</td>
                      <td>{{format(totalPrice3)}}</td>
                    </tr>
                    <tr>
                      <td>27</td>
                      <!-- <td>Đ/giá {{appraises.properties[0] && appraises.properties[0].property_detail ? appraises.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true).land_type_purpose.acronym : ''}} B.Quân.</td> -->
                      <td>Đ/giá {{showTypeLand(appraises)}} B.Quân.</td>
                      <td></td>
                      <td>{{format(dgd1)}}</td>
                      <td>{{format(dgd2)}}</td>
                      <td>{{format(dgd3)}}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <h3 class="mt-5" v-if="appraises.asset_general && appraises.asset_general.length > 0">BẢNG ĐIỀU CHỈNH CÁC YẾU TỐ SO SÁNH TSTĐG VÀ TSSS</h3>
                <div class="container-table" v-if="typeof appraises.asset_general !== 'undefined' && appraises.asset_general.length > 0">
                  <table class="table-property">
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
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1" :key="'appraisalLegalName' + index">
                        <strong>{{comparison_factor_appraise.name}}</strong>
                      </td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly'" :key="'appraisalLegal' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetInfo' + index"><span  v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'phap_ly'" :key="'phap_ly' + indexItem">{{comparison_factor.asset_title}}</span></td>
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phap_ly' && comparison_factor_appraise.status === 1" :key="'changeLegalRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexLegal) in appraises.asset_general" :key="'inputLegal' + indexLegal">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexLegal].comparison_factor" v-if="comparison_factor.type === 'phap_ly'" :key="'phap_ly' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">B</td>
                      <td><strong>Quy mô</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_mo'" :key="'quymo1' + index">{{comparison_factor_appraise.appraise_title}}m<sup>2</sup></td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetQuymo' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'quy_mo'" :key="'quymo2' +indexItem">{{comparison_factor.asset_title}}m<sup>2</sup></span></td>
                      <!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'quy_mo'" :key="'quy_mo' +index">{{comparison_factor.asset_title}}m</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_mo' && comparison_factor_appraise.status === 1" :key="'quymo3' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputQuymo' + indexStreet">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'quy_mo'" :key="'quymo4' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">C</td>
                      <td><strong>Chiều rộng mặt tiền</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_rong_mat_tien'" :key="'appraisalWidth' + index">{{comparison_factor_appraise.appraise_title}}m</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'chieu_rong_mat_tien' +indexItem">{{comparison_factor.asset_title}}m</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetWidth' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'chieu_rong_mat_tien' +index">{{comparison_factor.asset_title}}m</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_rong_mat_tien' && comparison_factor_appraise.status === 1" :key="'WidthRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexWidth) in appraises.asset_general" :key="'inputWidth' + indexWidth">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexWidth].comparison_factor" v-if="comparison_factor.type === 'chieu_rong_mat_tien'" :key="'chieu_rong_mat_tien' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">D</td>
                      <td><strong>Chiều sâu khu đất</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_sau_khu_dat'" :key="'appraisalDepth' + index">{{comparison_factor_appraise.appraise_title}}m</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' +indexItem">{{comparison_factor.asset_title}}m</span></td>
                      <!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetDepth' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' +index">{{comparison_factor.asset_title}}m</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'chieu_sau_khu_dat' && comparison_factor_appraise.status === 1" :key="'DepthRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexDepth) in appraises.asset_general" :key="'inputDepth' + indexDepth">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexDepth].comparison_factor" v-if="comparison_factor.type === 'chieu_sau_khu_dat'" :key="'chieu_sau_khu_dat' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">E</td>
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
                            class="label-none"
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
                      <td rowspan="3">F</td>
                      <td><strong>Giao thông</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'giao_thong'" :key="'appraisalTraffic' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'giao_thong'" :key="'giao_thong' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetTraffic' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'giao_thong'" :key="'ket_cau_duong' +index">{{comparison_factor.asset_title}}</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'giao_thong' && comparison_factor_appraise.status === 1" :key="'TrafficRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexTraffic) in appraises.asset_general" :key="'inputTraffic' + indexTraffic">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexTraffic].comparison_factor" v-if="comparison_factor.type === 'giao_thong'" :key="'giao_thong' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">G</td>
                      <td><strong>Kết cấu đường</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'ket_cau_duong'" :key="'ketcauduong1' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetKetcauduong' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ketcauduong2' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ket_cau_duong' +index">{{comparison_factor.asset_title}}m</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'ket_cau_duong' && comparison_factor_appraise.status === 1" :key="'ketcauduong3' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputKetcauduong' + indexStreet">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'ket_cau_duong'" :key="'ketcauduong4' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">H</td>
                      <td><strong>Độ rộng đường</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'do_rong_duong'" :key="'appraisalStreet' + index">{{comparison_factor_appraise.appraise_title}}m</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +indexItem">{{comparison_factor.asset_title}}m</span></td>
                      <!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +index">{{comparison_factor.asset_title}}m</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'do_rong_duong' && comparison_factor_appraise.status === 1" :key="'StreetRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputStreet' + indexStreet">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'do_rong_duong'" :key="'do_rong_duong' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">I</td>
                      <td><strong>Điều kiện hạ tầng</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_ha_tang'" :key="'appraisalConditions' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetConditions' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +index">{{comparison_factor.asset_title}}</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_ha_tang' && comparison_factor_appraise.status === 1" :key="'ConditionsRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexConditions) in appraises.asset_general" :key="'inputConditions' + indexConditions">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexConditions].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_ha_tang'" :key="'dieu_kien_ha_tang' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">J</td>
                      <td><strong>Kinh doanh</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'kinh_doanh'" :key="'appraisalBusiness' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetBusiness' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +index">{{comparison_factor.asset_title}}</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'kinh_doanh' && comparison_factor_appraise.status === 1" :key="'BusinessRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexBusiness) in appraises.asset_general" :key="'inputBusiness' + indexBusiness">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexBusiness].comparison_factor" v-if="comparison_factor.type === 'kinh_doanh'" :key="'kinh_doanh' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">K</td>
                      <td><strong>An ninh, môi trường sống</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'an_ninh_moi_truong_song'" :key="'appraisalSecurity' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetSecurity' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +index">{{comparison_factor.asset_title}}</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'an_ninh_moi_truong_song' && comparison_factor_appraise.status === 1" :key="'SecurityRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexSecurity) in appraises.asset_general" :key="'inputSecurity' + indexSecurity">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexSecurity].comparison_factor" v-if="comparison_factor.type === 'an_ninh_moi_truong_song'" :key="'an_ninh_moi_truong_song' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">L</td>
                      <td><strong>Phong thủy</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phong_thuy'" :key="'appraisalFengShui' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetFengShui' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +index">{{comparison_factor.asset_title}}</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'phong_thuy' && comparison_factor_appraise.status === 1" :key="'FengShuiRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexFengShui) in appraises.asset_general" :key="'inputFengShui' + indexFengShui">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexFengShui].comparison_factor" v-if="comparison_factor.type === 'phong_thuy'" :key="'phong_thuy' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">M</td>
                      <td><strong>Quy hoạch/Hiện trạng</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_hoach'" :key="'quyhoach1' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetQuyhoach' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'quy_hoach'" :key="'quyhoach2' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.comparison_factor" :key="'assetStreet' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'quy_hoach'" :key="'quy_hoach' +index">{{comparison_factor.asset_title}}m</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'quy_hoach' && comparison_factor_appraise.status === 1" :key="'quyhoach3' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexStreet) in appraises.asset_general" :key="'inputQuyhoach' + indexStreet">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexStreet].comparison_factor" v-if="comparison_factor.type === 'quy_hoach'" :key="'quyhoach4' +index">
                          <InputNumberFormat
                            class="label-none"
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
                      <td rowspan="3">N</td>
                      <td><strong>Điều kiện thanh toán</strong></td>
                      <td v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_thanh_toan'" :key="'appraisalPay' + index">{{comparison_factor_appraise.appraise_title}}</td>
                      <td v-for="(asset, index) in appraises.asset_general" :key="'assetLand' + index"><span v-for="(comparison_factor, indexItem) in appraises.comparison_factor[index].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +indexItem">{{comparison_factor.asset_title}}</span></td>
                      <!-- <td v-for="(asset, index) in appraises.asset_general" :key="'assetPay' + index"><span v-for="(comparison_factor, index) in asset.comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +index">{{comparison_factor.asset_title}}</span></td> -->
                    </tr>

                    <tr v-for="(comparison_factor_appraise, index) in appraises.comparison_factor[0].comparison_factor" v-if="comparison_factor_appraise.type === 'dieu_kien_thanh_toan' && comparison_factor_appraise.status === 1" :key="'PayRow' + index">
                      <td>Tỷ lệ điều chỉnh</td>
                      <td></td>
                      <td v-for="(asset, indexPay) in appraises.asset_general" :key="'inputPay' + indexPay">
                        <div v-for="(comparison_factor, index) in appraises.comparison_factor[indexPay].comparison_factor" v-if="comparison_factor.type === 'dieu_kien_thanh_toan'" :key="'dieu_kien_thanh_toan' +index">
                          <InputNumberFormat
                            class="label-none"
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
                    <td colspan="6">
                        <tr>
                          <td style="width:15.5%" rowspan="3">
                            <!-- <div style="margin-bottom:10px">O</div> -->
                            <button class="btn-delete" type="button" @click="handleDeleteOtherFactor(index, data_appraise.other_factor_asset)"><img src="../../../../assets/icons/ic_delete.svg" style="margin-right: 12px; color: red" alt="save"></button>
                          </td>
                          <td style="width:16.5%">
                            <InputText
                              class="inputLabel inputLabelCompare"
                              v-model="data_appraise.name"
                              vid="description"
                              styleInput="text-align:center;font-weight:bold"
                            @change="handleChangeNameFactor($event, data_appraise.other_factor_asset)"
                          />
                          </td>
                          <td style="width:23%">
                            <InputText
                              v-model="data_appraise.appraise_title"
                              vid="description"
                              styleInput="text-align:center"
                            @change="handleChangeTitleAppraise($event, data_appraise.other_factor_asset)"
                            />
                          </td>
                          <td v-for="(data_asset, indexItem) in data_appraise.other_factor_asset" :key="'yeutokhac2' +indexItem">
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
                          <td>Tỷ lệ điều chỉnh</td>
                          <td></td>
                          <td  v-for="(rate_asset, index) in data_appraise.other_factor_asset" :key="'yeutokhac4' +index">
                            <div>
                              <InputNumberFormat
                                class="label-none"
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
                        <td>Mức điều chỉnh</td>
                        <td></td>
                        <td v-for="(price_other, index) in price_other_comparison ? price_other_comparison[index] : []" :key="'yeutokhacprice' +index">
                          {{format(price_other.indication_price_asset) || 0}}
                        </td>
                        <!-- <td>{{format(priceYtk1)}}</td>
                        <td>{{format(priceYtk2)}}</td>
                        <td>{{format(priceYtk3)}}</td> -->
                      </tr>
                    </td>
                  </tr>

                  <tr class="other_button_container" >
                      <button class="btn btn-orange other_button" type="button" @click="handleAddOtherFactor" ><img src="../../../../assets/icons/ic_add-white.svg" style="margin-right: 12px" alt="save">Thêm yếu tố khác</button>
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
                      <td colspan="2"
                          :class="(mgcl1 > 15 || mgcl2 > 15 || mgcl3 > 15)  || (mgcl1 < -15 || mgcl2 < -15 || mgcl3 < -15) ? 'text-danger' : ''">
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

                    <tr>
                      <td>6</td>
                      <td colspan="2">Tổng số lần điều đỉnh (lần)</td>
                      <td>{{format(comparisonFactorChange1)}}</td>
                      <td>{{format(comparisonFactorChange2)}}</td>
                      <td>{{format(comparisonFactorChange3)}}</td>
                    </tr>
                    <tr v-if="((typeof indicativePrice !== 'undefined')&&(typeof indicativePrice[0] !== 'undefined'))">
                      <td>7</td>
                      <td colspan="2">Biên độ điều chỉnh (%)</td>
                      <td v-for="(asset, index_asset) in appraises.asset_general" :key="'asset_area_adjusted' + index_asset" >
                        <div v-for="(percent, index) in area_adjusted" :key="'percent_adjust' + index" v-if="percent.id === asset.id">
                          {{`${checkMin(area_adjusted[asset.id])} - ${checkMax(area_adjusted[asset.id])}`}}
                            <!-- {{area_adjusted[asset.id].min > area_adjusted[asset.id].max ? area_adjusted[asset.id].max : area_adjusted[asset.id].min }}% - {{area_adjusted[asset.id].max > area_adjusted[asset.id].min ? area_adjusted[asset.id].max : area_adjusted[asset.id].min}}% -->
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
                      <td colspan="2">Thống nhất mức giá chỉ dẫn</td>
                      <td colspan="3" align="center"><strong>{{format(mgtn)}}</strong></td>
                    </tr>
                    <tr>
                      <td>10</td>
                      <td colspan="2">Làm tròn</td>
                      <td colspan="3" align="center">{{ format(formatCurrent(mgtn))}}đ</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <h3 class="mt-4 text-center" v-if="appraises.asset_general && appraises.asset_general.length === 0">Không có tài sản so sánh để thực hiện điều chỉnh</h3>
          </div>
    </ValidationObserver>
  </div>
</template>

<script>
import {Tabs, TabItem} from 'vue-material-tabs'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputText from '@/components/Form/InputText'
import Certificate from '@/models/Certificate'
export default {
	name: 'ModalAppendixOne',
	props: ['formData', 'id'],
	data () {
		return {
			appraises: {},
			form: this.formData ? JSON.parse(JSON.stringify(this.formData)) : {},
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: [],
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			indicativePrice: [],
			otherFactor: 'Yếu tố khác',
			showOtherFactor: false,
			showError: false,
			detail1: null,
			detail2: null,
			detail3: null,
			dgxd1: 0,
			dgxd2: 0,
			dgxd3: 0,
			clcl1: 0,
			clcl2: 0,
			clcl3: 0,
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
			comparisonFactorMin1: 0,
			comparisonFactorMin2: 0,
			comparisonFactorMin3: 0,
			comparisonFactorMax1: 0,
			comparisonFactorMax2: 0,
			comparisonFactorMax3: 0,
			priceYtk1: 0,
			priceYtk2: 0,
			priceYtk3: 0,
			other_comparison: [],
			data_other_comparison: [],
			price_other_comparison: [],
			delete_other_comparison: [],
			otherIndex: 0,
			data_land_price: [],
			area_adjusted: {}
		}
	},
	components: {
		InputNumberFormat,
		Tabs,
		TabItem,
		InputText
	},
	computed: {
	},
	created () {
		this.getOtherComparison()
	},
	mounted () {
		this.getData()
		this.calculation(this.form)
		this.getIndicativePrice()
		this.getDataPrice(this.form.asset_unit_price)
	},
	methods: {
		showTypeLand (dataDetail) {
			let checkFacility = dataDetail.properties[0].property_detail.find(property_detail => property_detail.is_transfer_facility === true)
			if (checkFacility) {
				return checkFacility.land_type_purpose.acronym
			} else return ''
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
		async getDataPrice (data) {
			let formDataClone = JSON.parse(JSON.stringify(this.formData))
			await this.form.asset_unit_price.forEach((item, index) => {
				if (!item.update_value) {
					this.form.asset_unit_price[index].update_value = item.original_value
				}
			})
			const map = new Map()
			let indexTem = {}
			let get_id_transfer_facility = null
			let price_temp_appraise = null
			let type_temp_appraise = null
			let arrayCheckFacility = []
			await data.forEach(item => {
				if (!map.has(item.land_type_id)) {
					map.set(item.land_type_id, true)
					indexTem['id_check'] = item.land_type_id
					const price_UBND = this.form.asset_unit_price.filter(data => data.land_type_id === indexTem['id_check'])
					// tạo data ảo hiển thị
					if (formDataClone.properties && formDataClone.properties.length > 0) {
						formDataClone.properties.forEach(data_property => {
							data_property.property_detail.forEach(data_property_detail => {
								// TH là cơ sở
								if (data_property_detail.is_transfer_facility) {
									get_id_transfer_facility = data_property_detail.land_type_purpose_id
									price_temp_appraise = data_property_detail.circular_unit_price
									type_temp_appraise = data_property_detail.land_type_purpose.acronym
								} else {
									arrayCheckFacility.push({
										not_facility_id: data_property_detail.land_type_purpose_id,
										not_facility_price: data_property_detail.circular_unit_price,
										name_purpose: data_property_detail.land_type_purpose.acronym
									})
								}
							})
						})
					}
					// TH TSSS có chung MDSD phụ
					arrayCheckFacility.forEach(item_property => {
						let filterData = data.filter(item => item_property.not_facility_id === item.land_type_id)
						let filterCheckDataAdd = this.data_land_price.filter(item => item.name_purpose_land === item_property.name_purpose)
						if (item_property.not_facility_id === item.land_type_id && item_property.name_purpose === item.land_type_data.acronym) {
							this.data_land_price.push({
								price_appraise: item_property.not_facility_price,
								name_purpose_land: item.land_type_data.acronym,
								price_purpose_land: price_UBND
							})
						} else if (filterData && filterData.length === 0 && filterCheckDataAdd && filterCheckDataAdd.length === 0) {
							this.data_land_price.push({
								price_appraise: item_property.not_facility_price,
								name_purpose_land: item_property.name_purpose,
								price_purpose_land: []
							})
						}
					})
					// check type_land is exist of asset_unit_price
					let checkLandType = this.data_land_price.filter(itemCheck => itemCheck.name_purpose_land === item.land_type_data.acronym)
					// check type_land is exist of appraise
					let checkLandTypeAppraise = this.data_land_price.filter(itemCheck => itemCheck.name_purpose_land === type_temp_appraise)
					// TH TSSS có chung MDSD chính
					if (get_id_transfer_facility === item.land_type_id) {
						this.data_land_price.push({
							price_appraise: price_temp_appraise,
							name_purpose_land: item.land_type_data.acronym,
							price_purpose_land: price_UBND,
							facility: true
						})
					} else if (checkLandType.length === 0) {
						// lấy data k có đất của TSTD
						this.data_land_price.push({
							price_appraise: null,
							name_purpose_land: item.land_type_data.acronym,
							price_purpose_land: price_UBND
						})
						// lấy data đất cơ sở (TSTD) chưa có trong asset_unit_price
					} else if (get_id_transfer_facility !== item.land_type_id && checkLandTypeAppraise.length === 0) {
						this.data_land_price.push({
							price_appraise: price_temp_appraise,
							name_purpose_land: type_temp_appraise,
							price_purpose_land: [],
							facility: true
						})
					}
					arrayCheckFacility = []
					price_temp_appraise = null
					type_temp_appraise = null
					get_id_transfer_facility = null
				}
			})
			await this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showEror, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
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
			await this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
			await this.calculation(this.form)
		},
		// yếu tố so sanh khác
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
			const max = Math.max(...positionTem)
			if (!max || (max === -Infinity || max === Infinity)) {
				this.otherIndex = 0
			} else this.otherIndex = max
			await this.calculation(this.form)
			await this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showEror, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
		},
		calculation (asset) {
			let arrayPriceTem = []
			// thông tin TSSS
			let asset1 = (typeof asset.asset_general[0] !== 'undefined') ? asset.asset_general[0] : null
			let asset2 = (typeof asset.asset_general[1] !== 'undefined') ? asset.asset_general[1] : null
			let asset3 = (typeof asset.asset_general[2] !== 'undefined') ? asset.asset_general[2] : null

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

			// tính Đơn giá xây dựng của TSSS
			this.dgxd1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].unit_price_m2 : 0
			this.dgxd2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].unit_price_m2 : 0
			this.dgxd3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].unit_price_m2 : 0

			// lấy tỉ lệ Chất lượng còn lại của TSSS
			this.clcl1 = (typeof asset1.tangible_assets[0] !== 'undefined') ? asset1.tangible_assets[0].remaining_quality : 0
			this.clcl2 = (typeof asset2.tangible_assets[0] !== 'undefined') ? asset2.tangible_assets[0].remaining_quality : 0
			this.clcl3 = (typeof asset3.tangible_assets[0] !== 'undefined') ? asset3.tangible_assets[0].remaining_quality : 0

			// lấy giá cơ sở
			this.baseUnitPrice = 0
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
			this.detail1.property_detail.forEach((item, index) => {
				if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
					let circular_unit_price = item.circular_unit_price
					if (typeof (this.form.asset_unit_price) !== 'undefined') {
						this.form.asset_unit_price.forEach((item2, index2) => {
							if ((item2.asset_general_id === this.detail1.asset_general_id) && (item2.land_type_id === item.land_type_purpose) && (item2.position_type_id === item.position_type_id)) {
								if (item2.update === 2) {
									circular_unit_price = item2.update_value
								} else {
									circular_unit_price = item2.original_value
								}
							}
						})
					}
					let unitPrice = this.baseUnitPrice - parseFloat(circular_unit_price)
					this.price1 += unitPrice * parseFloat(item.total_area)
				}
			})
			// tính chi phí chuyển đổi TSSS-2
			this.price2 = 0
			if (typeof this.detail2.property_detail !== 'undefined') {
				this.detail2.property_detail.forEach((item, index) => {
					if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
						let circular_unit_price = item.circular_unit_price
						if (typeof (this.form.asset_unit_price) !== 'undefined') {
							this.form.asset_unit_price.forEach((item2, index2) => {
								if ((item2.asset_general_id === this.detail2.asset_general_id) && (item2.land_type_id === item.land_type_purpose) && (item2.position_type_id === item.position_type_id)) {
									if (item2.update === 2) {
										circular_unit_price = item2.update_value
									} else {
										circular_unit_price = item2.original_value
									}
								}
							})
						}
						let unitPrice = this.baseUnitPrice - parseFloat(circular_unit_price)
						this.price2 += unitPrice * parseFloat(item.total_area)
					}
				})
			}
			// tính chi phí chuyển đổi TSSS-3
			this.price3 = 0
			if (typeof this.detail3.property_detail !== 'undefined') {
				this.detail3.property_detail.forEach((item, index) => {
					if (item.land_type_purpose_data.acronym !== this.baseAcronym) {
						let circular_unit_price = item.circular_unit_price
						if (typeof (this.form.asset_unit_price) !== 'undefined') {
							this.form.asset_unit_price.forEach((item2, index2) => {
								if ((item2.asset_general_id === this.detail3.asset_general_id) && (item2.land_type_id === item.land_type_purpose) && (item2.position_type_id === item.position_type_id)) {
									if (item2.update === 2) {
										circular_unit_price = item2.update_value
									} else {
										circular_unit_price = item2.original_value
									}
								}
							})
						}
						let unitPrice = this.baseUnitPrice - parseFloat(circular_unit_price)
						this.price3 += unitPrice * parseFloat(item.total_area)
					}
				})
			}

			// tính giá trị Quyền sử dụng đất của TSSS
			this.totalPrice1 = ((typeof asset1.total_estimate_amount !== 'undefined') ? asset1.total_estimate_amount : 0) + this.price1 - (this.dgxd1 * this.dtsxd1 * this.clcl1 / 100)
			this.totalPrice2 = ((typeof asset2.total_estimate_amount !== 'undefined') ? asset2.total_estimate_amount : 0) + this.price2 - (this.dgxd2 * this.dtsxd2 * this.clcl2 / 100)
			this.totalPrice3 = ((typeof asset3.total_estimate_amount !== 'undefined') ? asset3.total_estimate_amount : 0) + this.price3 - (this.dgxd3 * this.dtsxd3 * this.clcl3 / 100)

			// tính đơn giá dất của TSSS
			this.dgd1 = (typeof this.detail1.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice1 / this.detail1.asset_general_land_sum_area) : 0
			this.dgd2 = (typeof this.detail2.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice2 / this.detail2.asset_general_land_sum_area) : 0
			this.dgd3 = (typeof this.detail3.asset_general_land_sum_area !== 'undefined') ? (this.totalPrice3 / this.detail3.asset_general_land_sum_area) : 0

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

			//
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

			if ((typeof comparisonFactor1['hinh_dang_dat'] !== 'undefined') && comparisonFactor1['hinh_dang_dat'].status === 1) {
				let percentHdd1 = (typeof comparisonFactor1['hinh_dang_dat'].adjust_percent !== 'undefined') ? comparisonFactor1['hinh_dang_dat'].adjust_percent : 0
				let percentHdd2 = (typeof comparisonFactor2['hinh_dang_dat'].adjust_percent !== 'undefined') ? comparisonFactor2['hinh_dang_dat'].adjust_percent : 0
				let percentHdd3 = (typeof comparisonFactor3['hinh_dang_dat'].adjust_percent !== 'undefined') ? comparisonFactor3['hinh_dang_dat'].adjust_percent : 0
				// mức điều chỉnh của yếu tố HÌNH DÁNG DẤT
				this.priceHdd1 = percentHdd1 * this.totalPricePL1 / 100
				this.priceHdd2 = percentHdd2 * this.totalPricePL2 / 100
				this.priceHdd3 = percentHdd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentHdd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentHdd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentHdd3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['ket_cau_duong'] !== 'undefined') && comparisonFactor1['ket_cau_duong'].status === 1) {
				let percentKcd1 = (typeof comparisonFactor1['ket_cau_duong'].adjust_percent !== 'undefined') ? comparisonFactor1['ket_cau_duong'].adjust_percent : 0
				let percentKcd2 = (typeof comparisonFactor2['ket_cau_duong'].adjust_percent !== 'undefined') ? comparisonFactor2['ket_cau_duong'].adjust_percent : 0
				let percentKcd3 = (typeof comparisonFactor3['ket_cau_duong'].adjust_percent !== 'undefined') ? comparisonFactor3['ket_cau_duong'].adjust_percent : 0
				// mức điều chỉnh của yếu tố KẾT CẤU ĐƯỜNG
				this.priceKcd1 = percentKcd1 * this.totalPricePL1 / 100 //
				this.priceKcd2 = percentKcd2 * this.totalPricePL2 / 100
				this.priceKcd3 = percentKcd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentKcd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentKcd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentKcd3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['kinh_doanh'] !== 'undefined') && comparisonFactor1['kinh_doanh'].status === 1) {
				let percentKd1 = (typeof comparisonFactor1['kinh_doanh'].adjust_percent !== 'undefined') ? comparisonFactor1['kinh_doanh'].adjust_percent : 0
				let percentKd2 = (typeof comparisonFactor2['kinh_doanh'].adjust_percent !== 'undefined') ? comparisonFactor2['kinh_doanh'].adjust_percent : 0
				let percentKd3 = (typeof comparisonFactor3['kinh_doanh'].adjust_percent !== 'undefined') ? comparisonFactor3['kinh_doanh'].adjust_percent : 0
				// mức điều chỉnh của yếu tố KINH DOANH
				this.priceKd1 = percentKd1 * this.totalPricePL1 / 100
				this.priceKd2 = percentKd2 * this.totalPricePL2 / 100
				this.priceKd3 = percentKd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentKd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentKd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentKd3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['dieu_kien_ha_tang'] !== 'undefined') && comparisonFactor1['dieu_kien_ha_tang'].status === 1) {
				let percentDkht1 = (typeof comparisonFactor1['dieu_kien_ha_tang'].adjust_percent !== 'undefined') ? comparisonFactor1['dieu_kien_ha_tang'].adjust_percent : 0
				let percentDkht2 = (typeof comparisonFactor2['dieu_kien_ha_tang'].adjust_percent !== 'undefined') ? comparisonFactor2['dieu_kien_ha_tang'].adjust_percent : 0
				let percentDkht3 = (typeof comparisonFactor3['dieu_kien_ha_tang'].adjust_percent !== 'undefined') ? comparisonFactor3['dieu_kien_ha_tang'].adjust_percent : 0
				// mức điều chỉnh của yếu tố ĐIỀU KIỆN HẠ TẦNG
				this.priceDkht1 = percentDkht1 * this.totalPricePL1 / 100
				this.priceDkht2 = percentDkht2 * this.totalPricePL2 / 100
				this.priceDkht3 = percentDkht3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentDkht1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDkht2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDkht3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['phong_thuy'] !== 'undefined') && comparisonFactor1['phong_thuy'].status === 1) {
				let percentPt1 = (typeof comparisonFactor1['phong_thuy'].adjust_percent !== 'undefined') ? comparisonFactor1['phong_thuy'].adjust_percent : 0
				let percentPt2 = (typeof comparisonFactor2['phong_thuy'].adjust_percent !== 'undefined') ? comparisonFactor2['phong_thuy'].adjust_percent : 0
				let percentPt3 = (typeof comparisonFactor3['phong_thuy'].adjust_percent !== 'undefined') ? comparisonFactor3['phong_thuy'].adjust_percent : 0
				// mức điều chỉnh của yếu tố PHONG THỦY
				this.pricePt1 = percentPt1 * this.totalPricePL1 / 100
				this.pricePt2 = percentPt2 * this.totalPricePL2 / 100
				this.pricePt3 = percentPt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentPt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentPt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentPt3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['dieu_kien_thanh_toan'] !== 'undefined') && comparisonFactor1['dieu_kien_thanh_toan'].status === 1) {
				let percentDktt1 = (typeof comparisonFactor1['dieu_kien_thanh_toan'].adjust_percent !== 'undefined') ? comparisonFactor1['dieu_kien_thanh_toan'].adjust_percent : 0
				let percentDktt2 = (typeof comparisonFactor2['dieu_kien_thanh_toan'].adjust_percent !== 'undefined') ? comparisonFactor2['dieu_kien_thanh_toan'].adjust_percent : 0
				let percentDktt3 = (typeof comparisonFactor3['dieu_kien_thanh_toan'].adjust_percent !== 'undefined') ? comparisonFactor3['dieu_kien_thanh_toan'].adjust_percent : 0
				// mức điều chỉnh của yếu tố ĐIỀU KIỆN THANH TOÁN
				this.priceDktt1 = percentDktt1 * this.totalPricePL1 / 100
				this.priceDktt2 = percentDktt2 * this.totalPricePL2 / 100
				this.priceDktt3 = percentDktt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentDktt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDktt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDktt3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['quy_mo'] !== 'undefined') && comparisonFactor1['quy_mo'].status === 1) {
				let percentQm1 = (typeof comparisonFactor1['quy_mo'].adjust_percent !== 'undefined') ? comparisonFactor1['quy_mo'].adjust_percent : 0
				let percentQm2 = (typeof comparisonFactor2['quy_mo'].adjust_percent !== 'undefined') ? comparisonFactor2['quy_mo'].adjust_percent : 0
				let percentQm3 = (typeof comparisonFactor3['quy_mo'].adjust_percent !== 'undefined') ? comparisonFactor3['quy_mo'].adjust_percent : 0
				// mức điều chỉnh của yếu tố QUY MÔ
				this.priceQm1 = percentQm1 * this.totalPricePL1 / 100
				this.priceQm2 = percentQm2 * this.totalPricePL2 / 100
				this.priceQm3 = percentQm3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentQm1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentQm2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentQm3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['chieu_rong_mat_tien'] !== 'undefined') && comparisonFactor1['chieu_rong_mat_tien'].status === 1) {
				let percentCrmt1 = (typeof comparisonFactor1['chieu_rong_mat_tien'].adjust_percent !== 'undefined') ? comparisonFactor1['chieu_rong_mat_tien'].adjust_percent : 0
				let percentCrmt2 = (typeof comparisonFactor2['chieu_rong_mat_tien'].adjust_percent !== 'undefined') ? comparisonFactor2['chieu_rong_mat_tien'].adjust_percent : 0
				let percentCrmt3 = (typeof comparisonFactor3['chieu_rong_mat_tien'].adjust_percent !== 'undefined') ? comparisonFactor3['chieu_rong_mat_tien'].adjust_percent : 0
				// mức điều chỉnh của yếu tố CHIỀU RỘNG MẶT TIỀN
				this.priceCrmt1 = percentCrmt1 * this.totalPricePL1 / 100
				this.priceCrmt2 = percentCrmt2 * this.totalPricePL2 / 100
				this.priceCrmt3 = percentCrmt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentCrmt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentCrmt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentCrmt3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['chieu_sau_khu_dat'] !== 'undefined') && comparisonFactor1['chieu_sau_khu_dat'].status === 1) {
				let percentCskd1 = (typeof comparisonFactor1['chieu_sau_khu_dat'].adjust_percent !== 'undefined') ? comparisonFactor1['chieu_sau_khu_dat'].adjust_percent : 0
				let percentCskd2 = (typeof comparisonFactor2['chieu_sau_khu_dat'].adjust_percent !== 'undefined') ? comparisonFactor2['chieu_sau_khu_dat'].adjust_percent : 0
				let percentCskd3 = (typeof comparisonFactor3['chieu_sau_khu_dat'].adjust_percent !== 'undefined') ? comparisonFactor3['chieu_sau_khu_dat'].adjust_percent : 0
				// mức điều chỉnh của yếu tố CHIỀU SÂU KHU ĐẤT
				this.priceCskd1 = percentCskd1 * this.totalPricePL1 / 100
				this.priceCskd2 = percentCskd2 * this.totalPricePL2 / 100
				this.priceCskd3 = percentCskd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentCskd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentCskd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentCskd3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['do_rong_duong'] !== 'undefined') && comparisonFactor1['do_rong_duong'].status === 1) {
				let percentDrd1 = (typeof comparisonFactor1['do_rong_duong'].adjust_percent !== 'undefined') ? comparisonFactor1['do_rong_duong'].adjust_percent : 0
				let percentDrd2 = (typeof comparisonFactor2['do_rong_duong'].adjust_percent !== 'undefined') ? comparisonFactor2['do_rong_duong'].adjust_percent : 0
				let percentDrd3 = (typeof comparisonFactor3['do_rong_duong'].adjust_percent !== 'undefined') ? comparisonFactor3['do_rong_duong'].adjust_percent : 0
				// mức điều chỉnh của yếu tố ĐỘ RỘNG ĐƯỜNG
				this.priceDrd1 = percentDrd1 * this.totalPricePL1 / 100
				this.priceDrd2 = percentDrd2 * this.totalPricePL2 / 100
				this.priceDrd3 = percentDrd3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentDrd1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentDrd2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentDrd3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['giao_thong'] !== 'undefined') && comparisonFactor1['giao_thong'].status === 1) {
				let percentGt1 = (typeof comparisonFactor1['giao_thong'].adjust_percent !== 'undefined') ? comparisonFactor1['giao_thong'].adjust_percent : 0
				let percentGt2 = (typeof comparisonFactor2['giao_thong'].adjust_percent !== 'undefined') ? comparisonFactor2['giao_thong'].adjust_percent : 0
				let percentGt3 = (typeof comparisonFactor3['giao_thong'].adjust_percent !== 'undefined') ? comparisonFactor3['giao_thong'].adjust_percent : 0
				// mức điều chỉnh của yếu tố GIAO THÔNG
				this.priceGt1 = percentGt1 * this.totalPricePL1 / 100
				this.priceGt2 = percentGt2 * this.totalPricePL2 / 100
				this.priceGt3 = percentGt3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentGt1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentGt2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentGt3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['an_ninh_moi_truong_song'] !== 'undefined') && comparisonFactor1['an_ninh_moi_truong_song'].status === 1) {
				let percentAnmts1 = (typeof comparisonFactor1['an_ninh_moi_truong_song'].adjust_percent !== 'undefined') ? comparisonFactor1['an_ninh_moi_truong_song'].adjust_percent : 0
				let percentAnmts2 = (typeof comparisonFactor2['an_ninh_moi_truong_song'].adjust_percent !== 'undefined') ? comparisonFactor2['an_ninh_moi_truong_song'].adjust_percent : 0
				let percentAnmts3 = (typeof comparisonFactor3['an_ninh_moi_truong_song'].adjust_percent !== 'undefined') ? comparisonFactor3['an_ninh_moi_truong_song'].adjust_percent : 0
				// mức điều chỉnh của yếu tố AN NINH, MÔI TRƯỜNG SỐNG
				this.priceAnmts1 = percentAnmts1 * this.totalPricePL1 / 100
				this.priceAnmts2 = percentAnmts2 * this.totalPricePL2 / 100
				this.priceAnmts3 = percentAnmts3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentAnmts1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentAnmts2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentAnmts3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['quy_hoach'] !== 'undefined') && comparisonFactor1['quy_hoach'].status === 1) {
				let percentQh1 = (typeof comparisonFactor1['quy_hoach'].adjust_percent !== 'undefined') ? comparisonFactor1['quy_hoach'].adjust_percent : 0
				let percentQh2 = (typeof comparisonFactor2['quy_hoach'].adjust_percent !== 'undefined') ? comparisonFactor2['quy_hoach'].adjust_percent : 0
				let percentQh3 = (typeof comparisonFactor3['quy_hoach'].adjust_percent !== 'undefined') ? comparisonFactor3['quy_hoach'].adjust_percent : 0
				// mức điều chỉnh của yếu tố AN NINH, MÔI TRƯỜNG SỐNG
				this.priceQh1 = percentQh1 * this.totalPricePL1 / 100
				this.priceQh2 = percentQh2 * this.totalPricePL2 / 100
				this.priceQh3 = percentQh3 * this.totalPricePL3 / 100

				this.comparisonFactorChange1 += (percentQh1 !== 0) ? 1 : 0
				this.comparisonFactorChange2 += (percentQh2 !== 0) ? 1 : 0
				this.comparisonFactorChange3 += (percentQh3 !== 0) ? 1 : 0
			}

			if ((typeof comparisonFactor1['yeu_to_khac'] !== 'undefined') && comparisonFactor1['yeu_to_khac'].status === 1) {
				let percentYtk1 = (typeof comparisonFactor1['yeu_to_khac'].adjust_percent !== 'undefined') ? comparisonFactor1['yeu_to_khac'].adjust_percent : 0
				let percentYtk2 = (typeof comparisonFactor2['yeu_to_khac'].adjust_percent !== 'undefined') ? comparisonFactor2['yeu_to_khac'].adjust_percent : 0
				let percentYtk3 = (typeof comparisonFactor3['yeu_to_khac'].adjust_percent !== 'undefined') ? comparisonFactor3['yeu_to_khac'].adjust_percent : 0
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
			let indexTem = []
			const map = new Map()
			this.price_other_comparison = []
			this.data_other_comparison.forEach(data => {
				data.other_factor_asset.forEach((item, index) => {
					if (!map.has(item.position)) {
						// set item
						map.set(item.position, true)
						indexTem['id_check'] = item.position
						// filter item which have same position
						const comparison_other = this.other_comparison.filter(data => data.position === indexTem['id_check'])
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
			// tính mức giá trung bình của MỨC GIÁ CHỈ DẪN
			this.mgtb = ((asset.asset_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.asset_general.length)) : 0
			let arrayMGTN = [this.mgcd1, this.mgcd2, this.mgcd3]
			if (this.form.unify_indicative_price_slug === 'thap-nhat') {
				this.mgtn = Math.min(...arrayMGTN)
			} else if (this.form.unify_indicative_price_slug === 'cao-nhat') {
				this.mgtn = Math.max(...arrayMGTN)
			} else if (this.form.unify_indicative_price_slug === 'trung-binh') {
				this.mgtn = ((asset.asset_general.length) > 0) ? ((this.mgcd1 + this.mgcd2 + this.mgcd3) / (asset.asset_general.length)) : 0
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
				let temp_comparison_factor = asset.comparison_factor.filter(item => item.type !== 'yeu_to_khac')
				if (this.other_comparison.length > 0 && temp_comparison_factor) {
					this.other_comparison.forEach(item => {
						temp_comparison_factor.push(item)
					})
				}
				// lấy tất cả giá trị % biên độ điều chỉnh
				const assetFilter = temp_comparison_factor.filter(item => item.asset_general_id === assetData.id && item.status === 1)
				this.area_adjusted[assetData.id] = {}
				let arrProcess = JSON.parse(JSON.stringify([...assetFilter]))
				let min = this.getArrayMin(arrProcess)
				let max = this.getArrayMax(arrProcess)
				this.area_adjusted[assetData.id]['id'] = assetData.id
				this.area_adjusted[assetData.id]['min'] = Math.abs(min)
				this.area_adjusted[assetData.id]['max'] = Math.abs(max)
			})
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
		handleDeleteOtherFactor (index, data) {
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
			this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
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
					// order_by: this.other_comparison.length + 1,
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
			await this.calculation(this.form)
			await this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
		},
		handleChangeTitleAsset (event, data) {
			if (data && data.length > 0) {
				this.other_comparison.forEach(item => {
					if (item.position === data.position && item.asset_general_id === data.asset_general_id) {
						item.asset_title = event
					}
				})
				this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison)
			}
		},
		handleChangeTitleAppraise (event, data) {
			this.other_comparison.forEach(item => {
				if (data && data.length > 0 && item.position === data[0].position) {
					item.appraise_title = event
				}
			})
			this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison)
		},
		handleChangeNameFactor (event, data) {
			this.other_comparison.forEach(item => {
				if (data && data.length > 0 && item.position === data[0].position) {
					item.name = event
				}
			})
			this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison)
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
			this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
		},
		getData () {
			if ((typeof this.form !== 'undefined') && (this.form !== null)) {
				this.appraises = {}
				let appraise = JSON.parse(JSON.stringify(this.formData))
				let asset_generals = []
				if (typeof appraise.asset_general !== 'undefined') {
					appraise.comparison_factor.forEach((data, index) => {
						if (data.type === 'yeu_to_khac' && data.asset_title === '' && data.appraise_title === '') {
							appraise.comparison_factor[index].asset_title = 'Không biết'
							appraise.comparison_factor[index].appraise_title = 'Không biết'
						}
					})
					appraise.asset_general.forEach(asset_general => {
						const comparison_factor_TSSS = appraise.comparison_factor.filter(comparison => comparison.asset_general_id === asset_general.id)
						asset_generals.push({
							id: asset_general.id,
							comparison_factor: comparison_factor_TSSS
						})
					})
					this.appraises = {
						id: appraise.id,
						comparison_factor: asset_generals,
						asset_general: appraise.asset_general,
						asset_type: appraise.asset_type,
						appraise_has_assets: appraise.appraise_has_assets,
						properties: appraise.properties,
						construction_company: appraise.construction_company
					}
				}
			}
		},
		getIndicativePrice () {
			this.indicativePrice = []
			let appraise = JSON.parse(JSON.stringify(this.formData))
			let comparisons = []
			let average = 0
			let unit_price = 0
			if (typeof appraise.comparison_factor !== 'undefined') {
				appraise.asset_general.forEach((asset_general, indexArray) => {
					let total = 0
					let totalLevel = 0
					let count = 0
					let minRate = null
					let maxRate = null
					// appraise.comparison_factor.forEach((comparisonLegal) => {
					//   if (comparisonLegal.type === 'phap_ly') {
					//     if (appraise.asset_general[indexArray].tangible_assets && appraise.asset_general[indexArray].tangible_assets.length > 0) {
					//       total = (((appraise.asset_general[indexArray].total_amount * (1 + appraise.asset_general[indexArray].adjust_percent / 100)) + appraise.appraise_has_assets[indexArray].asset_price - appraise.asset_general[indexArray].tangible_assets[0].unit_price_m2) / appraise.asset_general[indexArray].properties.find(property => property.id === appraise.appraise_has_assets[indexArray].asset_property_detail_id).asset_general_land_sum_area) * (1 + comparisonLegal.adjust_percent / 100)
					//       if (comparisonLegal.asset_general_id === asset_general.id) {
					//         let comparison = comparisonLegal
					//         if (comparison.type !== 'phap_ly') {
					//           total = total + ((((appraise.asset_general[indexArray].total_amount * (1 + appraise.asset_general[indexArray].adjust_percent / 100)) + appraise.appraise_has_assets[indexArray].asset_price - appraise.asset_general[indexArray].tangible_assets[0].unit_price_m2) / appraise.asset_general[indexArray].properties.find(property => property.id === appraise.appraise_has_assets[indexArray].asset_property_detail_id).asset_general_land_sum_area) * (1 + comparisonLegal.adjust_percent / 100)) * (comparison.adjust_percent / 100)
					//           totalLevel = totalLevel + ((((appraise.asset_general[indexArray].total_amount * (1 + appraise.asset_general[indexArray].adjust_percent / 100)) + appraise.appraise_has_assets[indexArray].asset_price - appraise.asset_general[indexArray].tangible_assets[0].unit_price_m2) / appraise.asset_general[indexArray].properties.find(property => property.id === appraise.appraise_has_assets[indexArray].asset_property_detail_id).asset_general_land_sum_area) * (1 + comparisonLegal.adjust_percent / 100)) * (comparison.adjust_percent / 100)
					//         }
					//       }
					//     } else {
					//       total = (((appraise.asset_general[indexArray].total_amount * (1 + appraise.asset_general[indexArray].adjust_percent / 100)) + appraise.appraise_has_assets[indexArray].asset_price) / appraise.asset_general[indexArray].properties.find(property => property.id === appraise.appraise_has_assets[indexArray].asset_property_detail_id).asset_general_land_sum_area) * (1 + comparisonLegal.adjust_percent / 100)
					//       if (comparisonLegal.asset_general_id === asset_general.id) {
					//         let comparison = comparisonLegal
					//         if (comparison.type !== 'phap_ly') {
					//           total = total + ((((appraise.asset_general[indexArray].total_amount * (1 + appraise.asset_general[indexArray].adjust_percent / 100)) + appraise.appraise_has_assets[indexArray].asset_price) / appraise.asset_general[indexArray].properties.find(property => property.id === appraise.appraise_has_assets[indexArray].asset_property_detail_id).asset_general_land_sum_area) * (1 + comparisonLegal.adjust_percent / 100)) * (comparison.adjust_percent / 100)
					//           totalLevel = totalLevel + ((((appraise.asset_general[indexArray].total_amount * (1 + appraise.asset_general[indexArray].adjust_percent / 100)) + appraise.appraise_has_assets[indexArray].asset_price) / appraise.asset_general[indexArray].properties.find(property => property.id === appraise.appraise_has_assets[indexArray].asset_property_detail_id).asset_general_land_sum_area) * (1 + comparisonLegal.adjust_percent / 100)) * (comparison.adjust_percent / 100)
					//         }
					//       }
					//     }
					//   }
					//   if (comparisonLegal.adjust_percent !== 0) {
					//     count++
					//   }
					//   if (comparisonLegal.adjust_percent !== 0 && maxRate === null) {
					//     maxRate = Math.abs(comparisonLegal.adjust_percent)
					//   }
					//   if (comparisonLegal.adjust_percent !== 0 && minRate === null) {
					//     minRate = Math.abs(comparisonLegal.adjust_percent)
					//   }
					//   if (comparisonLegal.adjust_percent !== 0 && Math.abs(comparisonLegal.adjust_percent) < minRate) {
					//     minRate = Math.abs(comparisonLegal.adjust_percent)
					//   }
					//   if (comparisonLegal.adjust_percent !== 0 && Math.abs(comparisonLegal.adjust_percent) > maxRate) {
					//     maxRate = Math.abs(comparisonLegal.adjust_percent)
					//   }
					// })
					comparisons.push({
						total: total,
						totalLevel: totalLevel,
						count: count,
						minRate: minRate,
						maxRate: maxRate
					})
				})
			}

			if (typeof appraise.construction_company_custom !== 'undefined') {
				appraise.construction_company_custom.forEach(construction_company => {
					unit_price = unit_price + construction_company.unit_price_m2
				})
			} else {
				unit_price = null
			}
			comparisons.forEach(total => {
				average = average + total.total
			})
			this.indicativePrice.push({
				comparisons: comparisons,
				average: average / comparisons.length,
				unit_price_m2: unit_price / appraise.construction_company_custom.length
			})
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
			return Math.round(value / 1000000) * 1000000
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			this.$emit('cancel', event)
			this.$emit('action', this.selectedRowKeys)
		},
		saveData () {
			// const dataSave = []
			// if (typeof this.appraises !== 'undefined') {
			//   this.appraises.comparison_factor.forEach(comparison => {
			//     comparison.comparison_factor.forEach(data => {
			//       dataSave.push(data)
			//     })
			//   })
			// }
			// this.postData(dataSave)
		},
		async postData (dataSave) {
			const res = await Certificate.postDataCertificate(dataSave)
			if (res.data) {
				this.$toast.open({
					message: 'Điều chỉnh phụ lục 1 thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$emit('cancel')
				this.$emit('action', this.formData.id)
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
			this.getIndicativePrice()
			this.$emit('handleInput', this.appraises.comparison_factor, this.id, this.showError, this.other_comparison, this.delete_other_comparison, this.form.asset_unit_price)
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
      overflow-y: auto;
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
      height: 100vh;
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
  }
  .container-table {
    border-radius: 5px;
    border: 1px solid #F3F2F7;
  }
  .other_button {
    width: 200px;
    margin-top: 15px;
  }
  .other_button_container {
    height: 90px;
  }
</style>
