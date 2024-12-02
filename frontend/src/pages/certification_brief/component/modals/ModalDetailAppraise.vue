<template>
  <div>
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="card">
          <div class="container-title">
            <div class="d-flex justify-content-between">
              <h2 class="title">Tỉ lệ chênh lệch tài sản thẩm định</h2>
              <img height="35px" @click="handleCancel" class="cancel" src="../../../../assets/icons/ic_cancel_2.svg" alt="">
            </div>
          </div>
          <div class="contain-detail">
            <Tabs v-if="data && data.length > 1" class="tab_contruction" :navAuto="true">
              <TabItem v-for="dataAppraise in data" :key="dataAppraise.id" :name="'TSTĐ_' + dataAppraise.id">
                <div class="row heigh_div w-100">
                  <div class="header_title col"></div>
                  <div class="header_title col">{{`TSTĐ_${dataAppraise.id}`}}
                    <button class="link-detail text-none" @click.prevent="handleDetail(dataAppraise.id)">{{`TSTĐ_${dataAppraise.id}`}}</button>
                  </div>
                  <div class="header_title col" ref="" v-for="item in dataAppraise.appraiseList" :key="item.id">
                      <button class="link-detail text-none" @click.prevent="handleDetail(item.id)">{{`TSTĐ_${item.id}`}}</button>
                  </div>
                </div>
                <div class="row heigh_div w-100 main_title header_title_detail">
                  <div class="col">Kết quả thẩm định</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Tổng giá trị tài sản</div>
                  <div class="content_details_assets color_content col">{{getPriceAppraise(dataAppraise, 'total_asset_price')}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{getPriceAppraise(item, 'total_asset_price')}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Giá trị đất ước tính</div>
                  <div class="content_details_assets color_content col">{{getPriceAppraise(dataAppraise, 'total_asset_price')}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{getPriceAppraise(item, 'total_asset_price')}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Đơn giá đất</div>
                  <div class="content_details_assets color_content col">{{formatNumber(parseFloat(dataAppraise.purposePrice).toFixed(0))}} đ</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{formatNumber(parseFloat(item.purposePrice).toFixed(0))}} đ</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Tỉ lệ chênh lệch đơn giá</div>
                  <div class="content_details_assets color_content col">-</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" :class="{'error_text': item.priority === 2, 'warning_text': item.priority === 1}" class="content_details_assets color_content col">{{parseFloat(item.risk).toFixed(0) + '%'}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="w-100 heigh_div col-12 title_details_assets header_title_detail">Thông tin chung</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Loại tài sản</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.asset_type.description}}</div>
                  <div class="content_details_assets color_content col" v-for="item in dataAppraise.appraiseList" :key="item.id">{{item.asset_type.description}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Vị trí</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.properties[0].front_side ? 'Mặt tiền' : 'Hẻm'}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].front_side ? 'Mặt tiền' : 'Hẻm'}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Bề rộng đường</div>
                  <div class="content_details_assets color_content col">{{formatNumber(dataAppraise.properties[0].front_side === 1 ? dataAppraise.properties[0].front_side_width : dataAppraise.properties[0].property_turning_time[dataAppraise.properties[0].property_turning_time.length - 1].main_road_length )}} m</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{formatNumber(item.properties[0].front_side === 1 ? item.properties[0].front_side_width : item.properties[0].property_turning_time[item.properties[0].property_turning_time.length - 1].main_road_length )}} m</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Chất liệu đường</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.properties[0].front_side === 1 ? dataAppraise.properties[0].material.description : dataAppraise.properties[0].property_turning_time[dataAppraise.properties[0].property_turning_time.length - 1].material.description}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].front_side === 1 ? item.properties[0].material.description : item.properties[0].property_turning_time[item.properties[0].property_turning_time.length - 1].material.description}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Khoảng cách</div>
                  <div class="content_details_assets color_content col">-</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{parseFloat(+item.distance * 1000).toFixed(0)}} m</div>
                </div>
                <div class="row heigh_div w-100 main_title header_title_detail">
                  <div class="col">Thông tin QSDĐ</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Loại đất</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.properties[0].property_detail && dataAppraise.properties[0].property_detail[0] ? dataAppraise.properties[0].property_detail[0].land_type_purpose.description : '-'}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].property_detail && item.properties[0].property_detail[0] ? item.properties[0].property_detail[0].land_type_purpose.description : '-'}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Tổng diện tích</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.properties[0].appraise_land_sum_area}} m<sup>2</sup></div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].appraise_land_sum_area}} m<sup>2</sup></div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Kích thước</div>
                  <div class="content_details_assets color_content col">{{`${dataAppraise.properties[0].front_side_width}m x ${dataAppraise.properties[0].insight_width}m`}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{`${item.properties[0].front_side_width}m x ${item.properties[0].insight_width}m`}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Hình dáng</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.properties[0].land_shape.description}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].land_shape.description}}</div>
                </div>
                <div class="row heigh_div w-100 main_title header_title_detail">
                  <div class="col">Thông tin CTXD</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Loại CTXD</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.tangible_assets && dataAppraise.tangible_assets.length > 0 && dataAppraise.tangible_assets[0].building_type ? dataAppraise.tangible_assets[0].building_type.description : '-'}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.tangible_assets && item.tangible_assets.length > 0 && item.tangible_assets[0].building_type ? item.tangible_assets[0].building_type.description : '-'}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Diện tích sàn</div>
                  <div class="content_details_assets color_content col">
                    <div v-if="dataAppraise.tangible_assets && dataAppraise.tangible_assets.length > 0 ">
                      {{formatNumber(dataAppraise.tangible_assets[0].total_construction_base)}} m<sup>2</sup>
                    </div>
                    <div v-else>-</div>
                  </div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">
                    <div v-if="item.tangible_assets && item.tangible_assets.length > 0 ">
                      {{formatNumber(item.tangible_assets[0].total_construction_base)}} m<sup>2</sup>
                    </div>
                    <div v-else>-</div>
                  </div>
                </div>
                <div class="row heigh_div w-100 main_title header_title_detail">
                  <div class="col">Nguồn thông tin</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Người thực hiện</div>
                  <div class="content_details_assets color_content col">{{dataAppraise.created_by.name}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.created_by.name}}</div>
                </div>
                <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Lần cập nhập cuối cùng</div>
                  <div class="content_details_assets color_content col">{{formatDate(dataAppraise.updated_at)}}</div>
                  <div v-for="item in dataAppraise.appraiseList" :key="item.id" class="content_details_assets color_content col">{{formatDate(item.updated_at)}}</div>
                </div>
              </TabItem>
            </Tabs>
            <div v-if="data && data.length === 1">
              <div class="row heigh_div w-100">
                <div class="header_title col"></div>
                <div class="header_title col">
                    <button class="link-detail text-none" @click.prevent="handleDetail(data[0])">{{`TSTĐ_${data[0].id}`}}</button>
              </div>
                <div class="header_title col" v-for="item in data[0].appraiseList" :key="item.id">
                    <button class="link-detail text-none" @click.prevent="handleDetail(item.id)">{{`TSTĐ_${item.id}`}}</button>
                </div>
              </div>
              <div class="row heigh_div w-100 main_title header_title_detail">
                <div class="col">Kết quả thẩm định</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Tổng giá trị tài sản</div>
                <div class="content_details_assets color_content col">{{getPriceAppraise(data[0], 'total_asset_price')}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{getPriceAppraise(item, 'total_asset_price')}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Giá trị đất ước tính</div>
                <div class="content_details_assets color_content col">{{getPriceAppraise(data[0], 'total_asset_price')}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{getPriceAppraise(item, 'total_asset_price')}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Đơn giá đất</div>
                <div class="content_details_assets color_content col">{{formatNumber(parseFloat(data[0].purposePrice).toFixed(0))}} đ</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{formatNumber(parseFloat(item.purposePrice).toFixed(0))}} đ</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Tỉ lệ chênh lệch đơn giá</div>
                <div class="content_details_assets color_content col">-</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" :class="{'error_text': item.priority === 2, 'warning_text': item.priority === 1}" class="content_details_assets color_content col">{{parseFloat(item.risk).toFixed(0) + '%'}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="w-100 heigh_div col-12 title_details_assets header_title_detail">Thông tin chung</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Loại tài sản</div>
                <div class="content_details_assets color_content col">{{data[0].asset_type.description}}</div>
                <div class="content_details_assets color_content col" v-for="item in data[0].appraiseList" :key="item.id">{{item.asset_type.description}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Vị trí</div>
                <div class="content_details_assets color_content col">{{data[0].properties[0].front_side ? 'Mặt tiền' : 'Hẻm'}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].front_side ? 'Mặt tiền' : 'Hẻm'}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Bề rộng đường</div>
                <div class="content_details_assets color_content col">{{formatNumber(data[0].properties[0].front_side === 1 ? data[0].properties[0].front_side_width : data[0].properties[0].property_turning_time[data[0].properties[0].property_turning_time.length - 1].main_road_length )}} m</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{formatNumber(item.properties[0].front_side === 1 ? item.properties[0].front_side_width : item.properties[0].property_turning_time[item.properties[0].property_turning_time.length - 1].main_road_length )}} m</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Chất liệu đường</div>
                <div class="content_details_assets color_content col">{{data[0].properties[0].front_side === 1 ? data[0].properties[0].material.description : data[0].properties[0].property_turning_time[data[0].properties[0].property_turning_time.length - 1].material.description}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].front_side === 1 ? item.properties[0].material.description : item.properties[0].property_turning_time[item.properties[0].property_turning_time.length - 1].material.description}}</div>
              </div>
              <div class="row heigh_div w-100">
                  <div class="title_details_assets col">Khoảng cách</div>
                  <div class="content_details_assets color_content col">-</div>
                  <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{parseFloat(+item.distance * 1000).toFixed(0)}} m</div>
                </div>
              <div class="row heigh_div w-100 main_title header_title_detail">
                <div class="col">Thông tin QSDĐ</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Loại đất</div>
                <div class="content_details_assets color_content col">{{data[0].properties[0].property_detail && data[0].properties[0].property_detail[0] ? data[0].properties[0].property_detail[0].land_type_purpose.description : '-'}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].property_detail && item.properties[0].property_detail[0] ? item.properties[0].property_detail[0].land_type_purpose.description : '-'}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Tổng diện tích</div>
                <div class="content_details_assets color_content col">{{data[0].properties[0].appraise_land_sum_area}} m<sup>2</sup></div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].appraise_land_sum_area}} m<sup>2</sup></div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Kích thước</div>
                <div class="content_details_assets color_content col">{{`${data[0].properties[0].front_side_width}m x ${data[0].properties[0].insight_width}m`}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{`${item.properties[0].front_side_width}m x ${item.properties[0].insight_width}m`}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Hình dáng</div>
                <div class="content_details_assets color_content col">{{data[0].properties[0].land_shape.description}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.properties[0].land_shape.description}}</div>
              </div>
              <div class="row heigh_div w-100 main_title header_title_detail">
                <div class="col">Thông tin CTXD</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Loại CTXD</div>
                <div class="content_details_assets color_content col">{{data[0].tangible_assets && data[0].tangible_assets.length > 0 && data[0].tangible_assets[0].building_type ? data[0].tangible_assets[0].building_type.description : '-'}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.tangible_assets && item.tangible_assets.length > 0 && item.tangible_assets[0].building_type ? item.tangible_assets[0].building_type.description : '-'}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Diện tích sàn</div>
                <div class="content_details_assets color_content col">
                  <div v-if="data[0].tangible_assets && data[0].tangible_assets.length > 0 ">
                    {{formatNumber(data[0].tangible_assets[0].total_construction_base)}} m<sup>2</sup>
                  </div>
                  <div v-else>-</div>
                </div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">
                  <div v-if="item.tangible_assets && item.tangible_assets.length > 0 ">
                    {{formatNumber(item.tangible_assets[0].total_construction_base)}} m<sup>2</sup>
                  </div>
                  <div v-else>-</div>
                </div>
              </div>
              <div class="row heigh_div w-100 main_title header_title_detail">
                <div class="col">Nguồn thông tin</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Người thực hiện</div>
                <div class="content_details_assets color_content col">{{data[0].created_by.name}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{item.created_by.name}}</div>
              </div>
              <div class="row heigh_div w-100">
                <div class="title_details_assets col">Lần cập nhập cuối cùng</div>
                <div class="content_details_assets color_content col">{{formatDate(data[0].updated_at)}}</div>
                <div v-for="item in data[0].appraiseList" :key="item.id" class="content_details_assets color_content col">{{formatDate(item.updated_at)}}</div>
              </div>
            </div>
          </div>
        </div>

      </div>
  </div>
</template>

<script>
import moment from 'moment'
import { Tabs, TabItem } from 'vue-material-tabs'
export default {
	name: 'ModalDetailAppraise',
	props: ['data'],
	data () {
		return { }
	},
	components: {
		TabItem,
		Tabs
	},
	computed: {},
	created () {
	},
	methods: {
		getPriceAppraise (item, slug) {
			if (slug === 'total_asset_price') {
				let check = item.asset_price.filter(itemPrice => itemPrice.slug === slug)
				if (check && check.length > 0) {
					return this.formatNumber(check[0].value) + ' đ'
				} else return '-'
			}
		},
		formatDate (date) {
			return moment(date).format('DD/MM/YYYY')
		},
		formatNumber (num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatArea (value) {
			let num = (value / 1).toString().replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleAction()
			}
		},
		async handleDetail (id) {
			let routeData
			routeData = this.$router.resolve({
				name: 'certification_asset.detail',
				query: { id: id }
			})
			window.open(routeData.href, '_blank')
		}
	},
	beforeMount () {

	}
}
</script>

<style lang="scss" scoped>
.title{
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 25px;
  color: #000000;
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
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1300px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    // padding: 35px 50px;
    padding: 25px 50px 65px;
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
    margin-top: 10px;
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
.img{
  margin-right: 13px;
}
.card{
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
  background: #FFFFFF;
  margin-bottom: 75px;
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
.card__order{
  max-width: 50%;
  margin-bottom: 1.25rem;
  @media (max-width: 767px) {
    max-width: 100%;
  }
}
.btn{
  &-white{
    max-height: none;

    line-height: 19.07px;
    margin-right: 15px;
    &:last-child{
      margin-right: 0;
    }
  }
  &-contain{
    margin-bottom: 55px;
  }
}
.d-grid{
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-column-gap: 8.9%;
  &:first-child {
    margin-top: 0;
  }
  &__checkbox{
    grid-template-columns: 1fr 1fr;
  }
  @media (max-width: 767px) {
    grid-template-columns: 1fr;
  }
}
.content{
  &-detail{
  }
    &-title{
      color: #555555;
      margin-bottom: 5px;
      font-weight: 500;
    }
  &-name{
    font-size: 1.125rem;
    color: #000000;
    margin-bottom: 15px;
    font-weight: 600;
    @media (max-width: 767px) {

    }
    &__code{
      color: #FAA831;
    }
  }
}
.contain-table{
  @media (max-width: 767px) {
    overflow-y: hidden;
    overflow-x: auto;
  }
  .table-property{
    width: 100%;
  }
}
.table-property{
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px 5px;
      font-weight: 500;
    }
  }
  tbody{
    td{
      border: 1px solid #E5E5E5;
      &:first-child{
        border-left: none;
        width: 180px
      }
      &:last-child{
        border-right: none;
      }
      box-sizing: border-box;
      padding: 14px;
    }
  }
}
.img-content{
  color: #000000;

  font-weight: 600;
  span{
    font-weight: 500;
    margin-left: 10px;
  }
}
.input-code{
  color: #000000;
  border-radius: 5px;
  width: 180px;
  border: 1px solid #000000;
  background: #f5f5f5;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
    transform: rotate(90deg);
    transition: .3s;
  }
}
.img-contain {
  aspect-ratio: 1/1;
  overflow: hidden;
  img{
    height: 100%;
    cursor: pointer;
    object-fit: cover;
  }
  &__table{
    margin: auto;
    max-width: 50px;
    max-height: 50px;
    img{
      object-fit: cover;
      object-position: top;
      cursor: pointer;
      display: flex;
      justify-content: center;
      max-width: 50px;
      max-height: 50px;
    }
  }
}
.container-title{
  margin: -35px -95px auto;
  // padding: 35px 95px 0;
  padding: 15px 50px 10px 95px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  .title{
    margin-top:20px;
    margin-bottom: 20px;
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
.container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
}
.traffic-light {
  color: black;
  padding: 0 5px;
  background: rgba(252,194,114,0.53);
  width: fit-content;
}
.input-switch__detail{
  margin-bottom: 25px;
}
.container-table {
    border-radius: 5px;
    border: 1px solid #F3F2F7;
}
.heigh_div {
  min-height: 35px;
  border-bottom: 1px solid #E8E8E8;
}
.header_title {
  background: #007EC6;
  color: #f5f5f5;
  font-weight: 600;
  padding-left: 1.2rem;
  padding-top: 0.5rem;
}
.content_details_assets {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  font-weight: 500;
}
.title_details_assets {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  font-weight: 600;
  color:#617F9E;
}
.header_title_detail{
  color: #3D4D65 !important;
  background-color: rgba(222, 230, 238, 0.5);
}
.main_title {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  font-weight: 600;
}
.error_text {
  color:red !important;
}
.warining_text {
  color:#fab005!important;
}
.row {
  margin-right: unset !important;
  margin-left: unset !important;
}
.link-detail {
			white-space: nowrap;
			text-transform: uppercase;
			background: transparent;
			border: none;
			cursor: pointer;
			&:hover,
			&:focus,
			&:active {
					color: #faa831;
					border: none;
					outline: none;
			}
	}
  .text-none {
		text-transform: none !important;
	}
</style>
