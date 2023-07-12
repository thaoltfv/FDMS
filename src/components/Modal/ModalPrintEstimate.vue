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
      <div class="card-body" id="printBody">
        <div class="text-right" style="color: #000">Mã sơ bộ: {{print_item.id? print_item.id : ''}}</div>
        <div class="title__property text-center">KẾT QUẢ ƯỚC TÍNH SƠ BỘ</div>
        <div class="w-100">
          <div class="card mb-3">
            <div class="card-header vendorListHeading">Mô tả tài sản</div>
            <div class="card-body" v-if="print_item.estimate_type !== 'CHUNG_CU'">
              <div class="data-row">
                <div class="data-label">Loại tài sản:</div>
                <div class="data-value">{{print_item.asset_type ? print_item.asset_type : 'Đất'}}</div>
              </div>
              <div class="data-row">
                <div class="data-label">Vị trí:</div>
                <div class="data-value">{{ print_item.position ? print_item.position : '' }}</div>
              </div>
              <div class="data-row">
                <div class="data-label">Tọa độ:</div>
                <div class="data-value">{{print_item.location ? print_item.location[0]+ ', ' + print_item.location[1] : '' }}</div>
              </div>
              <div class="data-row">
                <div class="data-label data-label--margin">Số tờ:</div>
                <div class="data-value">
                  <div class="d-inline-block">
                    <input type="text" class="form-control" v-model="print_item.doc_num" style="width: 150px">
                  </div>
                  <div class="d-inline-block">
                    <div class="font-weight-bold ml-4 mr-4 d-inline-block">Số thửa</div>
                    <input type="text" class="form-control d-inline-block" v-model="print_item.plot_num" style="width: 150px">
                  </div>
                </div>
              </div>
              <div class="data-row">
                <div class="data-label">Người yêu cầu:</div>
                <div class="data-value">{{print_item.user_request ? print_item.user_request : ''}}</div>
              </div>
            </div>
            <div class="table__body" v-if="print_item.estimate_type === 'CHUNG_CU'">
              <div>
                <div class="container__description container__description--apartment">
                  <div class="data-row data-row--apartment">
                    <div class="title">Chung cư:</div>
                    <div class="content">{{apartment_name}}</div>
                  </div>
                </div>
                <div class="container__description container__description--apartment">
                  <div class="data-row data-row--apartment">
                    <p class="title">Tầng:</p>
                    <p class="content">{{print_item.floor ? format(print_item.floor) : ''}}</p>
                  </div>
                  <div class="data-row data-row--apartment">
                    <p class="title">Block (khu):</p>
                    <p class="content">{{print_item.block}}</p>
                  </div>
                </div>
                <div class="container__description container__description--apartment">
                  <div class="data-row data-row--apartment">
                    <p class="title">Diện tích:</p>
                    <p class="content">{{print_item.area ? formatArea(print_item.area) : ''}}m<sup style="font-size: 11px">2</sup></p>
                  </div>
                  <div class="data-row data-row--apartment">
                    <p class="title">Số phòng ngủ:</p>
                    <p class="content">{{print_item.bedroom_num}}</p>
                  </div>
                </div>
                <div class="container__description container__description--apartment">
                  <div class="data-row data-row--apartment data-row--address">
                    <p class="title ">Địa chỉ:</p>
                    <p class="content content--full-address">{{print_item.position}}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="main-map">
            <div class="layer-map">
              <l-map
                ref="lmap"
                :zoom="zoom"
                :center="print_item.location"
                :options="{zoomControl: false}"
                :maxZoom="20"
              >
                <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                <l-control-zoom position="bottomright"></l-control-zoom>
                <l-marker :lat-lng="print_item.location">
                  <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                  <l-tooltip :options="{ permanent: true, interactive: true }">Vị trí tài sản</l-tooltip>
                </l-marker>
              </l-map>
            </div>
          </div>
          <div class="card mb-3" v-if="((print_item.recognized && print_item.recognized.length > 0 ) || (print_item.unrecognized && print_item.unrecognized.length > 0)) && print_item.estimate_type !== 'CHUNG_CU'">
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
              <tr v-if="print_item.unrecognized && print_item.unrecognized.length > 0" v-for="(unrecognized, index) in print_item.unrecognized" :key="'unrecognized'+index">
                <td class="text-center">Phần đất phù hợp quy hoạch</td>
                <td class="text-center">
                  <div class="text-center" v-if="print_item.landTypeUnrecognized">
                      {{ print_item.landTypeUnrecognized[index] ? print_item.landTypeUnrecognized[index].description : '' }}
                  </div>
                </td>
                <td class="text-center">{{formatArea(unrecognized.area)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center">{{format(unrecognized.average_land_unit_price) + ' đ' }}</td>
                <td class="text-center">{{format(unrecognized.estimate_price) + ' đ'}}</td>
              </tr>
              <tr v-if="print_item.recognized && print_item.recognized.length > 0" v-for="(recognize, index) in print_item.recognized" :key="'recognized'+index">
                <td class="text-center">Phần đất vi phạm quy hoạch</td>
                <td class="text-center">
                  <div class="text-center" v-if="print_item.landTypeRecognized">
                    {{ print_item.landTypeRecognized[index] ? print_item.landTypeRecognized[index].description : '' }}
                  </div>
                </td>
                <td class="text-center">{{formatArea(recognize.area)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center">{{format(recognize.average_land_unit_price) + ' đ' }}</td>
                <td class="text-center">{{format(recognize.estimate_price) + ' đ'}}</td>
              </tr>
              <tr class="summary">
                <td class="text-center">Tổng cộng:</td>
                <td class="text-center"></td>
                <td class="text-center">{{formatArea(print_item.total_area_land)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center"></td>
                <td class="text-center">{{format(print_item.total_land) + ' đ'}}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="card mb-3" v-if="print_item.building && print_item.building.length > 0 && print_item.estimate_type !== 'CHUNG_CU'">
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
              <tr v-for="(build, index) in print_item.building" :key="'building'+index">
                <td class="text-center">
                  <div class="text-center" v-if="print_item.buildingTangible">
                    {{print_item.buildingTangible[index] !== undefined && print_item.buildingTangible[index] !== null ? print_item.buildingTangible[index].description : ''}}
                  </div>
                </td>
                <td class="text-center">{{formatArea(build.area)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center">{{build.remaining_quality + '  %'}}</td>
                <td class="text-center">{{format(build.average_building_unit_price ) + ' đ'}}</td>
                <td class="text-center">{{format(build.estimate_price) + ' đ'}}</td>
              </tr>
              <tr class="summary">
                <td class="text-center">Tổng cộng:</td>
                <td class="text-center"></td>
                <td class="text-center">{{formatArea(print_item.total_area_building)}}m<sup style="font-size: 11px">2</sup></td>
                <td class="text-center"></td>
                <td class="text-center">{{format(print_item.total_building) + ' đ'}}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="text-danger mb-1" v-show="warningUpdate">
          *Đơn giá ước tính đã bị thay đổi. Bạn cần cập nhật để hệ thống tính lại tổng giá trị tài sản.
        </div>
        <div class="text-danger mb-2">
          *Kết quả ước tính sơ bộ chỉ có giá trị tham khảo và có thể thay đổi sau khi kiểm tra hiện trạng tài sản thực tế
        </div>
        <div v-if="!print_item.error_message && print_item.estimate_type === 'CHUNG_CU'">
          <div class="result-apartment">
            <div class="text-right result-total">ĐƠN GIÁ</div>
            <div class="text-right result-total-amount">{{ print_item.unit_price ? format(print_item.unit_price) : 0 }} VND/M<sup style="font-size: 11px">2</sup></div>
          </div>
          <div class="result-apartment">
            <div class="text-right result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
            <div class="text-right result-total-amount">{{ print_item.total_price ? format(print_item.total_price) : 0 }} VND</div>
          </div>
        </div>
        <div style="break-inside: avoid">
          <div class="report-info">
            <div class="report-label">Người ước tính:</div>
            <div class="report-value">{{created ? created : ''}}</div>
          </div>
          <div class="report-info">
            <div class="report-label">Thời điểm:</div>
            <div class="report-value">{{ createdAt ? createdAt : '' }}</div>
          </div>
          <div v-if="print_item.total_price > 0 && print_item.estimate_type !== 'CHUNG_CU'" >
            <div class="text-right result-total">TỔNG GIÁ TRỊ TÀI SẢN</div>
            <div class="text-right result-total-amount">{{ print_item.total_price ? format(print_item.total_price) : 0 }} VND</div>
          </div>
          <div v-if="print_item.error_message">
            <div class="text-right result-total-amount result-total-amount--error" v-html="print_item.error_message.replace('.', `.<br/>`)"></div>
          </div>
        </div>
      </div>
      <div class="card-footer footer-print">
        <button v-print="'printBody'" @click="statusPrint" class="btn btn-orange">In</button>
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
export default {
	name: 'ModalPrintEstimate',
	props: ['address', 'created', 'print_item', 'warningUpdate'],
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
			land_types: [],
			asset_details: [],
			createdAt: '',
			zoom: 15,
			caller: null,
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
			bounds: null
		}
	},
	mounted () {
		this.zoom = 16
		this.getCreatedAt()
		if (this.print_item.estimate_type === 'CHUNG_CU') {
			this.getApartments()
		}
	},
	methods: {
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

#printBody {
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
    &--error{
      margin-top: 10px;
      color: #EF3039;
      font-weight: 500;
      font-size: 23px;
    }
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
