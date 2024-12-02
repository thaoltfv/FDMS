<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <div class="card">
      <div class="card-header d-flex justify-content-end align-items-center">
        <img @click="handleCancel"
             src="../../assets/icons/ic_cancel-1.svg"
             alt="icon">
      </div>
      <div class="card-body" v-if="dataReport.length === 0">
        <h3 class="text-center" style="margin-bottom: 30px; margin-top: 30px">Không có dữ liệu để thực hiện bản in</h3>
      </div>
      <div class="card-body" id="printBodyLogs" v-if="dataReport.length > 0">
        <div class="text-right" style="color: #000">Mã sơ bộ: {{idLogs}}</div>
        <div class="title__property text-center">KẾT QUẢ ƯỚC TÍNH SƠ BỘ</div>
        <div class="w-100">
          <div class="main-map">
            <div class="layer-map">
              <l-map
                ref="lmap"
                :zoom="zoom"
                :center="[dataReport[0].report.location.split(',')[0], dataReport[0].report.location.split(',')[1]]"
                :options="{zoomControl: false}"
                :maxZoom="20"
              >
                <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                <l-control-zoom position="bottomright"></l-control-zoom>
                  <l-marker v-for="(report, index) in dataReport" :key="'map'+ index" :lat-lng="[report.report.location.split(',')[0],report.report.location.split(',')[1]]">
                    <l-icon class-name="someExtraClass">
                      <div class="marker-style font-weight-bold text-nowrap">
                        {{(index + 1)}}
                      </div>
                    </l-icon>
                  </l-marker>
              </l-map>
            </div>
          </div>
          <div style="break-inside: avoid">
            <div class="title">Mô tả tài sản</div>
            <div class="card mb-3">
              <table class="table-border">
                <thead class="vendorListHeading p-0">
                <tr>
                  <th class="text-center"></th>
                  <th class="text-center">Loại tài sản</th>
                  <th class="text-center">Tọa độ</th>
                  <th class="text-center">Số tờ</th>
                  <th class="text-center">Số thửa</th>
                  <th class="text-center">Vị trí</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(report, index) in dataReport" :key="'description'+ index">
                  <td class="text-nowrap">Tài sản {{index +1}}</td>
                  <td>{{report.report.estimate_type}}</td>
                  <td class="text-right">{{report.report.location}} </td>
                  <td>{{report.report.doc_no}}</td>
                  <td>{{report.report.land_no}}</td>
                  <td style="width: 255px">{{report.report.front_side === 'Hẻm' ? 'Tiếp giáp hẻm rộng khoảng ' + report.report.main_road_length +' m' : 'Tiếp giáp mặt tiền '}} {{report.report.street? report.report.street + ', ' : ''}} {{report.report.ward? report.report.ward + ', ' : ''}} {{report.report.district? report.report.district + ', ' : ''}} {{report.report.province? report.report.province : ''}}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="title">Kết quả ước tính</div>
          <div v-for="(report, index) in dataReport" :key="'reportDetail'+ index" style="break-inside: avoid">
            <div class="title-detail">Tài sản {{index + 1}}</div>
            <div class="card mb-3">
              <table class="table-border">
                <thead class="vendorListHeading p-0">
                <tr>
                  <th class="text-center">Quyền sử dụng đất</th>
                  <th class="text-center">Loại đất</th>
                  <th class="text-center">Diện tích </th>
                  <th class="text-center">Đơn giá</th>
                  <th class="text-center">Thành tiền</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(reportLand, index) in report.report_detail.land" :key="'reportLand'+ index">
                  <td >{{reportLand.type}}</td>
                  <td class="text-center">{{reportLand.land_type_purpose_name}}</td>
                  <td class="text-right">
                    <div v-if="reportLand.area">{{reportLand.area}}m<sup style="font-size: 11px">2</sup></div>
                    <div v-else></div>
                    </td>
                  <td class="text-right">{{reportLand.average_unit_price_update ? format(reportLand.average_unit_price_update) + ' đ' : format(reportLand.average_unit_price) + ' đ'}}</td>
                  <td class="text-right">{{reportLand.estimate_price_update ? format(reportLand.estimate_price_update) + ' đ' : format(reportLand.estimate_price) + ' đ'}}</td>
                </tr>
                </tbody>
                <thead class="vendorListHeading p-0" v-if="report.report_detail.building.length > 0">
                <tr>
                  <th class="text-center">Loại công trình</th>
                  <th class="text-center">Diện tích sàn</th>
                  <th class="text-center">% CLCL</th>
                  <th class="text-center">Đơn giá</th>
                  <th class="text-center">Thành tiền</th>
                </tr>
                </thead>
                <tbody v-if="report.report_detail.building.length > 0">
                <tr v-for="(reportBuilding, index) in report.report_detail.building" :key="'reportBuilding'+ index">
                  <td>{{reportBuilding.building_category}}</td>
                  <td class="text-right">
                    <div v-if="reportBuilding.area">{{reportBuilding.area}}m<sup style="font-size: 11px">2</sup></div>
                    <div v-else></div>
                  </td>
                  <td class="text-right">{{reportBuilding.remaining_quality + ' %'}}</td>
                  <td class="text-right">{{reportBuilding.average_unit_price_update ? format(reportBuilding.average_unit_price_update) + ' đ' : format(reportBuilding.average_unit_price) + ' đ'}}</td>
                  <td class="text-right">{{reportBuilding.estimate_price_update ? format(reportBuilding.estimate_price_update) + ' đ' : format(reportBuilding.estimate_price) + ' đ'}}</td>
                </tr>
                </tbody>
                <tbody class="row-last">
                <tr>
                  <td>Tổng cộng</td>
                  <td class="text-right">
                    <div v-if="dataTotal[index] && dataTotal[index].total_area && report.report_detail.building.length === 0">{{formatArea(dataTotal[index].total_area)}}m<sup style="font-size: 11px">2</sup></div>
                    <div v-else></div>
                  </td>
                  <td></td>
                  <td></td>
                  <td class="text-right">{{dataTotal[index] && dataTotal[index].total_price ? format(dataTotal[index].total_price) + ' đ' : 0 + ' đ'}}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div style="break-inside: avoid">
            <div class="title">Bảng tổng hợp kết quả</div>
            <div class="card mb-2">
              <table class="table-border">
                <thead class="vendorListHeading p-0">
                <tr>
                  <th class="text-center">Tên tài sản</th>
                  <th class="text-center">Tổng giá trị ước tính</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(report, index) in dataReport" :key="'reportTotal'+ index">
                  <td>Tài sản {{index + 1}}</td>
                  <td class="text-right">{{dataTotal[index] && dataTotal[index].total_price ? format(dataTotal[index].total_price) + ' đ' : 0 + ' đ'}}</td>
                </tr>
                </tbody>
              </table>
            </div>
            <div class="text-danger mb-2">
              *Kết quả ước tính sơ bộ chỉ có giá trị tham khảo và có thể thay đổi sau khi kiểm tra hiện trạng tài sản thực tế
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="w-100">
              <div class="report-info">
                <div class="report-label">Người ước tính:</div>
                <div class="report-value">{{created}}</div>
              </div>
              <div class="report-info">
                <div class="report-label">Người yêu cầu:</div>
                <div class="report-value text-capitalize"><span>{{user_request}}</span></div>
              </div>
              <div class="report-info">
                <div class="report-label">Thời điểm:</div>
                <div class="report-value">{{date ? formatDate(date) : createdAt}}</div>
              </div>
            </div>
            <div>
              <div class="text-right text-nowrap result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
              <div class="text-right text-nowrap result-total-amount">{{format(totalPrice)}} VND</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer footer-print" v-if="dataReport.length > 0">
        <button v-print="'printBodyLogs'" @click="statusPrint" class="btn btn-orange">In</button>
      </div>
    </div>
  </div>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import '../../../node_modules/leaflet/dist/leaflet.css';
</style>
<script>
import {LMap, LTileLayer, LMarker, LIcon, LControlZoom, LPopup, LCircle, LTooltip, LLayerGroup} from 'vue2-leaflet'
import WareHouse from '../../models/WareHouse'
import print from 'vue-print-nb'
import moment from 'moment'
export default {
	name: 'ModalPrintEstimates',
	props: ['created', 'dataReport', 'idLogs', 'date'],
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
		LLayerGroup
	},
	data () {
		return {
			apartment_name: '',
			dataTotal: [],
			land_types: [],
			asset_details: [],
			user_request: '',
			createdAt: '',
			totalPrice: 0,
			center: [10.964112, 106.856461],
			zoom: 15,
			caller: null,
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
			bounds: null
		}
	},
	mounted () {
		this.zoom = 16
		this.getCreatedAt()
		this.getTotal()
	},
	methods: {
		getTotal () {
			let user_requests = []
			let users = []
			this.dataReport.forEach(report => {
				let total = 0
				let total_area = 0
				report.report_detail.land.forEach(land => {
					if (land.estimate_price_update) {
						total = total + land.estimate_price_update
					} else {
						total = total + land.estimate_price
					}
					total_area = total_area + parseFloat(land.area)
				})
				report.report_detail.building.forEach(building => {
					if (building.estimate_price_update) {
						total = total + building.estimate_price_update
					} else {
						total = total + building.estimate_price
					}
				})
				this.dataTotal.push(
					{
						total_price: total,
						total_area: total_area
					}
				)
				if (report.report.user_request !== undefined && report.report.user_request !== null) {
					user_requests.push(
						report.report.user_request.toLowerCase()
					)
				}
				users = [...new Set(user_requests)]
			})
			users.forEach((user, index) => {
				if (index !== users.length - 1) {
					this.user_request += user + ', '
				} else {
					this.user_request += user
				}
			})
			this.getTotalPrice()
		},
		getTotalPrice () {
			this.dataTotal.forEach(total => {
				this.totalPrice = this.totalPrice + total.total_price
			})
		},
		async getApartments () {
			try {
				const resp = await WareHouse.getApartment()
				this.apartments = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
			this.getApartmentName()
		},
		getApartmentName () {
			this.apartments.forEach(apartment => {
				if (apartment.id === this.print_item.apartment_id) {
					this.apartment_name = apartment.name
				}
			})
		},
		getCreatedAt () {
			const today = new Date()
			this.createdAt = `${today.getHours() < 10 ? '0' + today.getHours() : today.getHours()}` + ':' + `${today.getMinutes() < 10 ? '0' + today.getMinutes() : today.getMinutes()}` + ' ' + `${today.getDate() < 10 ? '0' + today.getDate() : today.getDate()}` + '/' + `${(today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)}` + '/' + today.getFullYear()
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		statusPrint (event) {
			this.$emit('action', event)
		},
		handleAction (event) {
			this.$emit('action', event)
			this.$emit('cancel', event)
		}
	}
}
</script>

<style lang="scss" scoped>

#printBodyLogs {
  padding: 0 50px;
  * {
    text-align: left;
    @media print {
      -webkit-print-color-adjust: exact;
    }
  }

  .card {
    border-radius: 5px;
    border: 1px solid rgba(110, 117, 130, 0.2);
    box-shadow: none;
    overflow: hidden;

    * {
      font-size: 14px;
      color: #000000;
      font-weight: 500;
    }
  }
  .title {
    font-size: 22px;
    margin-bottom: 10px;
    font-weight: 600;
    color: #000000;
  }
  .vendorListHeading {
    &:first-child {
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }
    background: #FAA831;
    tr {
      th {
        font-weight: bold;
        color: #FFFFFF;
      }
    }
  }

  .card-header {
    border-bottom: none;
    padding: 10px;
  }

  .card-body {
    padding: 0;
  }

  .data-row {
    border-bottom: 1px solid rgba(110, 117, 130, 0.2);
    display: inline-block;
    width: 100%;
    box-sizing: border-box;

    &:nth-last-child(1) {
      border-bottom: none;
    }
    &--apartment{
      width: 50%;
      float: left;
      border-bottom: none;
      .title, .content {
        float: left;
        display: inline-block;
        width: 50%;
        box-sizing: border-box;
      }
    }
    &--address {
      width: 100%;
      .title{
        width: 25%;
      }
      .content {
        width: 75%;
      }
    }
  }
  .title {
    color: #000000;
  }
  .data-label {
    display: inline-block;
    font-weight: bold;
    padding: 5px 10px;
    width: 20%;
    box-sizing: border-box;
    float: left;
    &--margin{
      margin-top: 5px;
    }
  }

  .data-value {
    display: inline-block;
    padding: 5px 10px;
    width: 80%;
    box-sizing: border-box;
    float: right;

    input {
      padding: 3px 5px;
    }
  }

  td, th {
    padding: 10px;
  }

  tr {
    border-bottom: 1px solid rgba(110, 117, 130, 0.2);
    &:nth-last-child(1) {
      border-bottom: none;
    }
  }

  .summary {
    background: #FEF3E8;

    td {
      color: #000000;
      font-weight: bold;
    }
  }

  .report-info {
    display: inline-block;
    width: 100%;
    color: #000000;
    .report-label {
      white-space: nowrap;
      width: 35%;
      display: inline-block;
      box-sizing: border-box;
      font-weight: bold;
      float: left;
    }

    .report-value {
      width: 65%;
      display: inline-block;
      box-sizing: border-box;
      font-weight: bold;
      float: right;
    }
  }

  .result-total {
    color: #F28C1C;
    font-weight: bold;
    font-size: 15px;
    text-align: right;
  }

  .result-total-amount {
    font-weight: bold;
    font-size: 30px;
    color: #F28C1C;
    text-align: right;
  }
  .result-apartment{
    width: 100%;
    display: inline-block;
    border-bottom: 1px solid rgba(110, 117, 130, 0.2);
    .result-total-amount, .result-total {
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
      border-bottom: 1px solid #D0D0D0;
      &:last-child {
        border-bottom: none;
      }
      .title, .content {
        padding: 0;
        color: #000000;
        margin-bottom: 0;
      }
      .title{
        font-weight: 600;
        font-size: 14px;
      }
      .content{
        white-space: nowrap;
        @media (max-width: 767px) {
          white-space: normal;
        }
        &--full-address{
          white-space: normal;
        }
      }
      &--apartment{
        display: inline-block;
        float: left;
        width: 100%;
      }
    }
  }
}
.table-border {
  thead {
    border: 1px solid rgba(242, 140, 28, 0.59);
    tr {
      th {
        font-weight: 700;
        border-right: 1px solid rgba(110,117,130,0.2);
        &:last-child{
          border-right: none;
        }
      }
    }
  }
  tbody {
    color: #0b0d10;
    border: 1px solid rgba(242, 140, 28, 0.59);
    border-bottom: none;
    tr {
      td {
        border-right: 1px solid rgba(110,117,130,0.2);
        &:last-child {
          border-right: none;
        }
      }
    }
  }
  border-bottom: 2px solid rgba(242, 140, 28, 0.59);
  border-radius: 5px;
}
.footer-print {
  display: flex;
  justify-content: flex-end;
  padding: 0.75rem 50px;
}
.modal-delete {
  position: fixed;
  z-index: 10003;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, .6);

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
    border: 1px solid #2d2d2d;
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

        .name, .content {
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
            color: #FAA831;
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
  height: 275px;
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
.icon_marker{
  width: 25px;
}
  .title-detail {
    color: #000000;
    font-weight: 600;
    font-size: 16px;
  }
  .marker-style{
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    box-sizing: border-box;
    width: 25px;
    height: 25px;
    text-align: center;
    background: #cd201f !important;
    color: #ffffff;
  }
  .row-last{
    color: #000000;
    background: rgba(242,140,28,0.1);
    border-top: 1px solid rgba(110,117,130,0.2);
    tr {
      td {
        font-weight: bold !important;
      }
    }
  }
@page
{
  size: auto !important;
  margin: 8mm 0 5mm 0 !important;
}
</style>
