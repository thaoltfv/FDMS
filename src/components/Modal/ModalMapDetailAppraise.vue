<template>
  <div>
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="loading" :class="{'loading__true': isSubmit}">
          <a-spin />
        </div>
        <div class="card" :style="isMobile() ? {'margin-top':'-55px', 'max-height': '94vh', 'min-height': '94vh'} : {}">
          <div class="container-title">
            <div class="d-flex justify-content-between align-items-center">
              <h2 class="title">Thông tin chi tiết</h2>
              <img @click="handleCancel" class="cancel" src="../../assets/icons/ic_cancel-1.svg" alt="">
            </div>
          </div>
          <div class="contain-detail">
            <div class="card-body container-img" v-if="pic.length > 0">
              <div class=" d-flex justify-content-center">
                <div class="image-container">
                  <img :src="`${pic[activeImage].link}`" alt="">
                </div>
              </div>
            </div>
            <div class="card-body container-empty card-table" v-if="pic.length === 0">
              <div class="img-empty">
                <img src="../../assets/images/img_emply.svg" alt="">
                <h3>Không có hình ảnh để hiển thị</h3>
              </div>
            </div>
            <div class="card-footer" v-if="pic.length > 0">
              <div class="image-list-container" v-if="pic">
                <div v-for="(item, index) in pic" v-bind:key="index"
                     :class="activeImage === index ? 'image-control active' : 'image-control'"
                     @click="onChangeImageIndex(index)">
                  <img :src="`${item.link}`" alt="">
                </div>
              </div>
            </div>
          <div class="card-table">
            <div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title">Mô tả chung</h3>
                <img class="img-dropdown" :class="show_description? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_description = !show_description">
              </div>
            </div>
            <div class="card-body card-info card-land" v-if="show_description && property.properties && property.properties.length > 0">
                <div class="property-content row">
                  <p class="property-title col-4">Mã: </p>
                  <button class="link-detail property-detail property-detail__id col" @click="handleOpenCertificateAsset(property.id)">{{'TSTD' + '_' + property.id}}</button>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Vị trí: </p>
                  <p class="property-detail col">{{property.properties[0].front_side ? 'Mặt tiền' : 'Hẻm'}}</p>
                </div>
                <div class="property-content row" v-if="property.properties[0].front_side">
                  <p class="property-title col-4">Bề rộng đường: </p>
                  <p class="property-detail col">{{formatFloat(property.properties[0].main_road_length) + ' m'}}</p>
                </div>
                <div class="property-content row" v-if="property.properties[0].front_side === 0">
                  <p class="property-title col-4">Bề rộng hẻm: </p>
                  <p class="property-detail col">{{formatFloat(property.properties[0].property_turning_time[property.properties[0].property_turning_time.length - 1].main_road_length) + ' m'}}</p>
                </div>
                <div class="property-content row" v-if="property.street && property.ward && property.district && property.province">
                  <p class="property-title col-4">Địa chỉ: </p>
                  <p class="property-detail col">{{`${property.street.name}, ${property.ward.name}, ${property.district.name}, ${property.province.name}`}}</p>
                </div>
                <div class="property-content row" v-if="property.appraise_law && property.appraise_law.length > 0">
                  <p class="property-title col-4">Số tờ: </p>
                  <p class="property-detail col">
                    {{property.tothua && property.tothua.length > 0 ? (property.tothua.length === 1 ? property.tothua[0].doc_no : 'Nhiều tờ') : 'Không có'}}
                    </p>
                </div>
                <div class="property-content row" v-if="property.appraise_law && property.appraise_law.length > 0">
                  <p class="property-title col-4">Số thửa: </p>
                  <p class="property-detail col">
                    {{property.tothua && property.tothua.length > 0 ? (property.tothua.length === 1 ? property.tothua[0].land_no : 'Nhiều thửa') : 'Không có'}}
                    </p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Loại tài sản: </p>
                  <p class="property-detail col">{{property.asset_type !== undefined && property.asset_type !== null ? property.asset_type.description : ''}}</p>
                </div>
              <div class="property-content row">
                <p class="property-title col-4">Giá: </p>
                <p class="property-detail col">{{format(totalPrice)}}đ</p>
              </div>
              <div class="property-content row">
                <p class="property-title col-4">Ngày xác thực: </p>
                <p class="property-detail col">{{formatDate(property.updated_at)}}</p>
              </div>
            </div>
          </div>
          <div class="card-table">
            <div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title">Thông tin đất</h3>
                <img class="img-dropdown" :class="show_info_land ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_info_land = !show_info_land">
              </div>
            </div>
            <div class="card-body card-info card-land" v-if="show_info_land && property.properties && property.properties.length > 0">
              <div v-for="properties in property.properties" :key="properties.id">
                <div v-for="(property_detail, index) in properties.property_detail" :key="property_detail.id">
                  <div class="property-content row">
                    <p class="property-title col-4">Loại đất {{index + 1}}: </p>
                    <p class="property-detail col">{{property_detail.land_type_purpose ? property_detail.land_type_purpose.description : ''}}</p>
                  </div>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Tổng diện tích:</p>
                  <p class="property-detail col">{{format(properties.appraise_land_sum_area)}} m <sup>2</sup></p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Kích thước: </p>
                  <p class="property-detail col">{{properties.front_side_width}} m x {{properties.insight_width}} m</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Hình dáng: </p>
                  <p class="property-detail col">{{properties.land_shape ? properties.land_shape.description : ''}}</p>
                </div>
              </div>
            </div>
          </div>
            <div class="card-table" v-if="property.asset_type_id === 38 && property.tangible_assets.length > 0">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Thông tin công trình</h3>
                  <img class="img-dropdown" :class="show_tangible_asset ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_tangible_asset = !show_tangible_asset">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="show_tangible_asset">
                <div v-for="(tangible_asset) in property.tangible_assets" :key="tangible_asset.id">
                  <div class="property-content row">
                    <p class="property-title col-4">Loại công trình :</p>
                    <p class="property-detail col">{{tangible_asset.building_type.description}}</p>
                  </div>
                </div>
              </div>
            </div>
          <div class="card-table">
            <div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title">Kết quả thẩm định</h3>
                <img class="img-dropdown" :class="show_amount ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_amount = !show_amount">
              </div>
            </div>
            <div class="card-body card-info card-land" v-if="show_amount">
              <div class="property-content row">
                <p class="property-title col-4">Tổng giá trị: </p>
                <p class="property-detail col">{{format(totalPrice) + 'đ'}}</p>
              </div>
              <div class="property-content row"  v-if="property.asset_type_id === 38">
                <p class="property-title col-4">Giá trị công trình: </p>
                <p class="property-detail col">{{format(tangiblePrice) + 'đ'}}</p>
              </div>
              <div class="property-content row"  v-if="property.asset_type_id !== 39">
                <p class="property-title col-4">Giá trị QSDĐ: </p>
                <p class="property-detail col">{{format(landPrice) + 'đ'}}</p>
              </div>
              <div v-for="(properties, index) in property.properties" :key="index">
                <div v-for="(property_detail, index) in properties.property_detail" :key="index">
                  <div class="property-content row" >
                    <p class="property-title col-4">Đơn giá đất: </p>
                    <p class="property-detail col"><b>{{format(detailLandPrice(property_detail.land_type_purpose.acronym)) + 'đ ' + '(' + property_detail.land_type_purpose.description + ')'}}</b></p>
                  </div>
                </div>
              </div>
              <!-- <div class="property-content row" v-if="property.asset_type_id === 39">
                <p class="property-title col-4">Đơn giá bình quân căn hộ: </p>
                <p class="property-detail col"><b>{{ format(property.room_details[0].unit_price)}}đ</b></p>
              </div> -->
            </div>
          </div>
            <div class="card-table">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Hồ sơ thẩm định</h3>
                  <img class="img-dropdown" :class="show_source_info ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_source_info = !show_source_info">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="property.certificate">
                <div class="property-content row">
                  <p class="property-title col-4">Loại giao dịch: </p>
                  <p class="property-detail color-primary col">Đã thẩm định</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Mã HSTĐ: </p>
                  <button class="link-detail property-detail col" role="link" @click.prevent="handleOpenCertificateBrief(property.certificate.id)">HSTD_{{property.certificate.id}}</button>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Số chứng thư: </p>
                  <p class="property-detail col">{{property.certificate.certificate_num}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Ngày chứng thư: </p>
                  <p class="property-detail col">{{property.certificate.certificate_date ? formatDate(property.certificate.certificate_date) : ''}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Ngày thẩm định: </p>
                  <p class="property-detail col">{{property.certificate.appraise_date ? formatDate(property.certificate.appraise_date) : ''}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Chuyên viên thẩm định: </p>
                  <p class="property-detail col">{{property.certificate.appraiser_perform ? property.certificate.appraiser_perform.name : '' }}</p>
                </div>
              </div>
            </div>
        </div>
          <!-- <div class="container__print">
            <button class="btn btn-orange btn-orange--print mr-2" type="button" @click="print(property.id)">Tải xuống</button>
            <button class="btn btn-orange btn-orange--print" type="button" @click="openModalPrint(property.id)">In</button>
          </div> -->
      </div>
    </div>
    <ModalPrint
      v-if="openPrint"
      v-bind:print_detail="printDetail"
      @cancel="openPrint = false"
    />
  </div>
</template>

<script>
import moment from 'moment'
import WareHouse from '@/models/WareHouse'
import ModalPrint from '@/components/Modal/ModalPrint'
export default {
	name: 'ModalMapDetailAppraise',
	props: ['property', 'pic'],
	data () {
		return {
			printDetail: '',
			openPrint: false,
			isSubmit: false,
			front_side_content: '',
			show_info_apartment: true,
			show_tangible_asset: true,
			show_description: true,
			show_info_land: true,
			show_source_info: true,
			show_amount: true,
			activeImage: 0,
			activeName: 0
		}
	},
	components: {
		ModalPrint
	},
	mounted () {
		this.findFrontSide()
	},
	computed: {
		totalPrice () {
			let price = 0
			let round = 0
			let priceData = this.property.asset_price.find(i => i.slug === 'total_asset_price')
			let roundData = this.property.asset_price.find(i => i.slug === 'round_appraise_total')
			if (priceData) {
				price = priceData.value
			}
			if (roundData) {
				round = roundData.value
			}
			return this.roundPrice(price, round) || 0
		},
		landPrice () {
			let price = 0
			let round = 0
			let priceData = this.property.asset_price.find(i => i.slug === 'land_asset_price')
			let roundData = this.property.asset_price.find(i => i.slug === 'round_total')
			if (priceData) {
				price = priceData.value
			}
			if (roundData) {
				round = roundData.value
			}
			return this.roundPrice(price, round) || 0
		},
		tangiblePrice () {
			let price = 0
			let priceData = this.property.asset_price.find(i => i.slug === 'tangible_asset_price')
			if (priceData) {
				price = priceData.value
			}
			return price || 0
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
		handleOpenCertificateAsset (id) {
			let routeData = this.$router.resolve({
				name: 'certification_asset.detail',
				query: { id: id }
			})
			window.open(routeData.href, '_blank')
		},
		handleOpenCertificateBrief (id) {
			let routeData = this.$router.resolve({
				name: 'certification_brief.detail',
				query: { id: id }
			})
			window.open(routeData.href, '_blank')
		},
		detailLandPrice (acronym) {
			let price = 0
			let round = 0
			let priceData = this.property.asset_price.find(i => i.slug === 'land_asset_purpose_' + acronym + '_price')
			let roundData = this.property.asset_price.find(i => i.slug === 'land_asset_purpose_' + acronym + '_round')
			if (priceData) {
				price = priceData.value
			}
			if (roundData) {
				round = roundData.value
			}
			return this.roundPrice(price, round) || 0
		},
		roundPrice (value, roundPrice) {
			if (!value) {
				return 0
			}
			if (roundPrice && roundPrice > 0 && roundPrice <= 7) {
				let round = Math.pow(10, roundPrice)
				return Math.ceil(value / round) * round
			} else if (roundPrice && roundPrice < 0 && roundPrice >= -7) {
				let round = Math.pow(10, Math.abs(roundPrice))
				return Math.floor(value / round) * round
			} else return parseFloat(value.toFixed(0))
		},
		async getPrint (id) {
			const resp = await WareHouse.getPrintPdf(id)
			this.printDetail = resp.data.url
		},
		async openModalPrint (data) {
			this.isSubmit = true
			await this.getPrint(data)
			this.openPrint = true
			this.isSubmit = false
		},
		async print (id) {
			this.isSubmit = true
			await WareHouse.getPrint(id).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				}
				this.isSubmit = false
			})
		},
		findFrontSide () {
			let front_side = this.property.properties.find(property => property.front_side === 1)
			if (front_side !== undefined && front_side !== null) {
				this.front_side_content = 'Mặt tiền'
			} else {
				this.front_side_content = 'Hẻm'
			}
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		onChangeImageIndex (index) {
			this.activeImage = index
			this.activeName = index
		}
	}
}
</script>

<style lang="scss" scoped>
  .title{
   font-size: 16px;
   font-weight: 700;
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
    max-width: 672px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 20px;
    .contain-detail{
      overflow-y: auto;
      overflow-x: hidden;
      margin-top: 20px;
      &::-webkit-scrollbar{
        width: 2px;
      }
    }
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
    }
  }
  .card-table{
    border-radius: 5px;
    background: #FFFFFF;
  }
  .card{
    .contain-detail{
      overflow-y: auto;
      overflow-x: hidden;
      margin-top: 20px;
      &::-webkit-scrollbar{
        width: 2px;
      }
    }
    &-title{
      background: #F3F2F7;
      padding: 7px 15px;
      margin-bottom: 10px;
      .title{
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 0;
        color: #F28C1C;
      }
    }
    &-table{
      border-radius: 5px;
      background: #FFFFFF;
      margin: 0 auto 50px;
    }
    &-body{
      padding: 35px 30px 40px;
    }
    &-info{
      .title{
        font-size: 16px;
        font-weight: 700;
        margin-top: 28px;
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .property {
    &-content{
      margin: 5px 0;
      color: #000000;
      font-size: 14px;
    }
    &-title {
      font-weight: 600;
    }
    &-detail {
      &__id {
        color: #FAA831;
      }
    }
  }
  .color-blue {
    color: #37C3F4;
  }

  .color-purple {
    color: #8659FA;
  }

  .color-orange {
    color: #FAA831;
  }

  .color-green {
    color: #1F8B24;
  }
  .cancel{
    cursor: pointer;
  }
  .img-empty{
    width: auto !important;
    text-align: center;
    img{
      max-width: 100px;
    }
    h3{
      margin-top: 10px;
      color: #000000;
    }
  }
  .container-empty{
    border: 1px solid #000000;
  }
  .container-img{
    padding: 10px;
  }
  .container{
    &__print{
      margin-top: 10px;
      display: flex;
      justify-content: end;
    }
  }
  .card-footer{
    padding: 0;
  }
  .image-list-container {
    overflow: auto;
    text-align: center;
    display: flex;

    .image-control {
      max-width: 60px;
      min-width: 60px;
      max-height: 60px;
      min-height: 60px;
      margin: 5px;
      display: inline-block;
      cursor: pointer;
      border: 3px solid #FFFFFF;

      &.active {
        border-color: #FAA831;
      }

      img {
        height: 100%;
        width: 100%;
        object-fit: cover;
      }
    }
  }

  .image-container {
    width: 600px;
    height: 321px;
    display: flex;
    align-content: center;
    justify-content: center;
    @media (max-width: 670px) {
      width: 90vw;
      height: 90vw;
    }

    img {
      object-fit: contain;
    }
  }

  .link-detail {
    white-space: nowrap;
    text-transform: uppercase;
    background: transparent;
    border: none;
    text-align: left;
    &:hover,
    &:focus,
    &:active {
      color: #faa831;
      border: none;
      outline: none;
    }
  }

  .image-caption {
    color: #000000;
    display: block;
  }

  .btn-change {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 50%;
    aspect-ratio: 1/1;
    display: flex;
    align-items: center;
    justify-content: center;

    img {
      height: 20px;
    }

    &--prev {
      left: 30px;
    }

    &--next {
      right: 30px;
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 14px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
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

</style>
