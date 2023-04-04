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
      <div class="card-body" id="printBodyID">
        <div class="text-right" style="color: #000">Mã sơ bộ: {{print_item.id}}</div>
        <div class="title__property text-center">KẾT QUẢ ƯỚC TÍNH SƠ BỘ</div>
        <div class="w-100">
          <div class="card mb-3">
            <div class="card-header vendorListHeading">Mô tả tài sản</div>
            <div class="card-body">
              <div class="data-row">
                <div class="data-label">Loại tài sản:</div>
                <div class="data-value">{{print_item.report.estimate_type}}</div>
              </div>
              <div class="data-row">
                <div class="data-label">Vị trí:</div>
                <div class="data-value">{{print_item.report.front_side === 'Hẻm' ? 'Tiếp giáp hẻm rộng khoảng ' + print_item.report.main_road_length +' m' : 'Tiếp giáp mặt tiền '}} {{print_item.report.street? print_item.report.street + ', ' : ''}} {{print_item.report.ward? print_item.report.ward + ', ' : ''}} {{print_item.report.district? print_item.report.district + ', ' : ''}} {{print_item.report.province? print_item.report.province : ''}}</div>
              </div>
              <div class="data-row">
                <div class="data-label">Tọa độ:</div>
                <div class="data-value">{{print_item.report.location}}</div>
              </div>
              <div class="data-row">
                <div class="data-label mt-0">Số tờ:</div>
                <div class="data-value">
                  <div class="d-inline-block">
                    <div style="width: 150px">
                      {{print_item.report.doc_no}}
                    </div>
                  </div>
                  <div class="d-inline-block">
                    <div class="font-weight-bold ml-4 mr-4 d-inline-block">Số thửa</div>
                    <div class="d-inline-block" style="width: 150px"> {{print_item.report.land_no ? print_item.report.land_no : '' }}</div>
                  </div>
                </div>
              </div>
              <div class="data-row">
                <div class="data-label">Người yêu cầu:</div>
                <div class="data-value">{{print_item.report.user_request ? print_item.report.user_request : ''}}</div>
              </div>
            </div>
          </div>
          <div class="main-map">
            <div class="layer-map">
              <l-map
                ref="lmap"
                :zoom="zoom"
                :center="[print_item.report.location.split(',')[0], print_item.report.location.split(',')[1]]"
                :options="{zoomControl: false}"
                :maxZoom="20"
              >
                <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                <l-control-zoom position="bottomright"></l-control-zoom>
                <l-marker :lat-lng="[print_item.report.location.split(',')[0], print_item.report.location.split(',')[1]]">
                  <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                  <l-tooltip :options="{ permanent: true, interactive: true }">Vị trí tài sản</l-tooltip>
                </l-marker>
              </l-map>
            </div>
          </div>
          <div class="card mb-3" v-if="print_item.report_detail.land.length > 0">
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
              <tr v-for="(land, index) in print_item.report_detail.land" :key="'land' + index">
                <td class="text-center">{{land.type}}</td>
                <td class="text-center">
                  {{land.land_type_purpose_name}}
                </td>
                <td class="text-center">{{formatArea(land.area)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center">{{land.average_unit_price_update ? format(land.average_unit_price_update) + ' đ' : format(land.average_unit_price) + ' đ' }}</td>
                <td class="text-center">{{land.estimate_price_update ?  format(land.estimate_price_update) + ' đ' : format(land.estimate_price) + ' đ'}}</td>
              </tr>
              <tr class="summary">
                <td class="text-center">Tổng cộng:</td>
                <td class="text-center"></td>
                <td class="text-center">{{formatArea(areaLand)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center"></td>
                <td class="text-center">{{format(print_item.report.total_land_price_update) + ' đ'}}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="card mb-3" v-if="print_item.report_detail.building.length > 0">
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
              <tr v-for="(building, index) in print_item.report_detail.building" :key="'building' + index">
                <td class="text-center">
                  {{building.building_category}}
                </td>
                <td class="text-center">{{formatArea(building.area)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center">{{building.remaining_quality + '  %'}}</td>
                <td class="text-center">{{building.average_unit_price_update ? format(building.average_unit_price_update) + ' đ' : format(building.average_unit_price) + ' đ'}}</td>
                <td class="text-center">{{building.estimate_price_update ? format(building.estimate_price_update) + ' đ' : format(building.estimate_price) + ' đ'}}</td>
              </tr>
              <tr class="summary">
                <td class="text-center">Tổng cộng:</td>
                <td class="text-center">{{formatArea(areaBuilding)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center">{{format(print_item.report.total_building_price_update) + ' đ'}}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="text-danger mb-2">
          *Kết quả ước tính sơ bộ chỉ có giá trị tham khảo và có thể thay đổi sau khi kiểm tra hiện trạng tài sản thực tế
        </div>
        <div style="break-inside: avoid">
          <div class="report-info">
            <div class="report-label">Người ước tính:</div>
            <div class="report-value">{{print_item.report.create_by ? print_item.report.create_by : ''}}</div>
          </div>
          <div class="report-info">
            <div class="report-label">Thời điểm:</div>
            <div class="report-value">{{ print_item.report.create_date ?  formatDate(print_item.report.create_date) : '' }}</div>
          </div>
          <div>
            <div class="text-right result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
            <div class="text-right result-total-amount">{{print_item.report.total_price_update ?  format(print_item.report.total_price_update) : format(print_item.report.total_price) }} VND</div>
          </div>
        </div>
      </div>
      <div class="card-footer footer-print">
        <button v-print="'printBodyID'" class="btn btn-orange">In</button>
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
import print from 'vue-print-nb'
import moment from 'moment'
export default {
	name: 'ModalPrintEstimateLog',
	props: ['address', 'created', 'print_item'],
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
			areaLand: 0,
			areaBuilding: 0,
			createdAt: '',
			zoom: 15,
			caller: null,
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			bounds: null
		}
	},
	mounted () {
		this.getTotalArea()
		this.zoom = 16
	},
	methods: {

		getTotalArea () {
			let totalAreaLand = 0
			let totalAreaBuilding = 0
			if (this.print_item.report_detail.land.length > 0) {
				this.print_item.report_detail.land.forEach(land => {
					if (land.area !== '' && land.area !== undefined && land.area !== null) {
						totalAreaLand = totalAreaLand + parseFloat(land.area)
					}
				})
			} else {
				totalAreaLand = 0
			}
			if (this.print_item.report_detail.building.length > 0) {
				this.print_item.report_detail.building.forEach(building => {
					if (building.area !== '' && building.area !== undefined && building.area !== null) {
						totalAreaBuilding = totalAreaBuilding + parseFloat(building.area)
					}
				})
			} else {
				totalAreaBuilding = 0
			}
			this.areaLand = totalAreaLand
			this.areaBuilding = totalAreaBuilding
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

#printBodyID {
  padding: 30px 50px;

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
  .vendorListHeading {
    background: #F28C1C;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    font-weight: bold;
    color: #FFFFFF;
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
    border-right: 1px solid rgba(110,117,130,0.2);
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
      width: 20%;
      display: inline-block;
      box-sizing: border-box;
      font-weight: bold;
      float: left;
    }

    .report-value {
      width: 80%;
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
    border: 1px solid #D0D0D0;
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
  height: 200px;
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
</style>
