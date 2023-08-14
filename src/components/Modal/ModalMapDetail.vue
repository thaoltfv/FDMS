<template>
  <div>
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="loading" :class="{'loading__true': isSubmit}">
          <a-spin />
        </div>
        <div class="card">
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
            <div class="card-body card-info card-land" v-if="show_description">
                <div class="property-content row">
                  <p class="property-title col-4">Mã: </p>
                  <a v-if="property.migrate_status == 'TSS'" class="property-detail property-detail__id col" :href="`/property/detail?id=${property.id}`" target='_blank'>{{property.migrate_status + '_' + property.id}}</a>
                  <a v-else class="property-detail property-detail__id col" :href="`certification_asset/real-estate/detail?id=${property.id}`" target='_blank'>{{property.migrate_status + '_' + property.id}}</a>
                </div>
                <div class="property-content row" v-if="property.asset_type_id === 39">
                  <p class="property-title col-4">Tên chung cư: </p>
                  <p class="property-detail col" v-if="property.project !== undefined && property.project !== null" >{{property.project.name}}</p>
                </div>
                <div class="property-content row" v-if="property.asset_type_id !== 39">
                  <p class="property-title col-4">Vị trí: </p>
                  <p class="property-detail col">{{front_side_content}}</p>
                </div>
                <div class="property-content row" v-if="property.properties && property.properties.length > 0 && property.properties[property.properties.length - 1].compare_property_turning_time && property.properties[property.properties.length - 1].compare_property_turning_time.length > 0 && property.asset_type_id !== 39">
                  <p class="property-title col-4">Bề rộng hẻm: </p>
                  <p class="property-detail col">{{formatFloat(property.properties[property.properties.length - 1].compare_property_turning_time[property.properties[property.properties.length - 1].compare_property_turning_time.length - 1].main_road_length) + ' m'}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Địa chỉ: </p>
                  <p class="property-detail col">{{property.full_address !== undefined && property.full_address !== null ? property.full_address : ''}}</p>
                </div>
                <div class="property-content row" v-if="property.asset_type_id !== 39">
                  <p class="property-title col-4">Số tờ: </p>
                  <!-- <p class="property-detail col">{{property.land_no ? property.doc_no : 'Không có'}}</p> -->
                  <p class="property-detail col">{{property.properties[0].compare_property_doc[0].doc_num ? property.properties[0].compare_property_doc[0].doc_num : 'Không có'}}</p>
                </div>
                <div class="property-content row" v-if="property.asset_type_id !== 39">
                  <p class="property-title col-4">Số thửa: </p>
                  <!-- <p class="property-detail col">{{property.land_no ? property.land_no : 'Không có'}}</p> -->
                  <p class="property-detail col">{{property.properties[0].compare_property_doc[0].plot_num ? property.properties[0].compare_property_doc[0].plot_num : 'Không có'}}</p>

                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Loại tài sản: </p>
                  <p class="property-detail col">{{property.asset_type !== undefined && property.asset_type !== null ? property.asset_type.description : ''}}</p>
                </div>
              <div class="property-content row">
                <p class="property-title col-4">Giá: </p>
                <p class="property-detail col">{{format(property.total_amount !== undefined && property.total_amount !== null ? property.total_amount : '')}}đ</p>
              </div>
              <div class="property-content row" v-if="property.asset_type_id === 39">
                <p class="property-title col-4">Ngày xác thực: </p>
                <p class="property-detail col">{{formatDate(property.updated_at)}}</p>
              </div>
            </div>
          </div>
          <div class="card-table" v-if="property.asset_type_id === 39">
            <div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title">Thông tin căn hộ:</h3>
                <img class="img-dropdown" :class="show_info_apartment ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_info_apartment = !show_info_apartment">
              </div>
            </div>
            <div class="card-body card-info card-land" v-if="show_info_apartment">
              <div class="property-content row">
                <p class="property-title col-4">Khu (Block):</p>
                <p class="property-detail col"> {{property.block !== undefined && property.block !== null ? property.block.name : 'Chưa có thông tin'}}</p>
              </div>
              <div class="property-content row">
                <p class="property-title col-4">Mã căn hộ:</p>
                <p class="property-detail col"> {{ property.apartment ? property.apartment.name : '' }} </p>
              </div>
              <div class="property-content row">
                <p class="property-title col-4">Tầng:</p>
                <p class="property-detail col"> {{property.apartment_specification.total_floors}}</p>
              </div>
              <div class="property-content row">
                <p class="property-title col-4">Diện tích:</p>
                <p class="property-detail col"> {{property.room_details[0].area}} m<sup>2</sup></p>
              </div>
              <div class="property-content row">
                <p class="property-title col-4">Số phòng ngủ:</p>
                <p class="property-detail col"> {{property.room_details[0].bedroom_num}}</p>
              </div>
            </div>
          </div>
          <div class="card-table" v-if="property.asset_type_id !== 39">
            <div class="card-title">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="title">Thông tin đất</h3>
                <img class="img-dropdown" :class="show_info_land ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_info_land = !show_info_land">
              </div>
            </div>
            <div class="card-body card-info card-land" v-if="show_info_land">
              <div v-for="properties in property.properties" :key="properties.id">
                <div v-for="(property_detail, index) in properties.property_detail" :key="property_detail.id">
                  <div class="property-content row">
                    <p class="property-title col-4">Loại đất {{index + 1}}: </p>
                    <p class="property-detail col">{{property_detail.land_type_purpose_data !== undefined && property_detail.land_type_purpose_data !== null ? property_detail.land_type_purpose_data.description : ''}}</p>
                  </div>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Tổng diện tích:</p>
                  <p class="property-detail col">{{format(properties.asset_general_land_sum_area)}} m <sup>2</sup></p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Kích thước: </p>
                  <p class="property-detail col">{{properties.front_side_width}} m x {{properties.insight_width}} m</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Hình dáng: </p>
                  <p class="property-detail col">{{property.topographic_data!== undefined && property.topographic_data !==null ? property.topographic_data.description : ''}}</p>
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
                <h3 class="title">Giá trị BĐS</h3>
                <img class="img-dropdown" :class="show_amount ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_amount = !show_amount">
              </div>
            </div>
            <div class="card-body card-info card-land" v-if="show_amount">
              <div class="property-content row">
                <p class="property-title col-4">Giá trị sau điều chỉnh: </p>
                <p class="property-detail col">{{format(property.total_estimate_amount) + 'đ'}}</p>
              </div>
              <div class="property-content row"  v-if="property.asset_type_id === 38">
                <p class="property-title col-4">Giá trị công trình: </p>
                <p class="property-detail col">{{format(property.total_construction_amount) + 'đ'}}</p>
              </div>
              <div class="property-content row"  v-if="property.asset_type_id !== 39">
                <p class="property-title col-4">Giá trị đất ước tính: </p>
                <p class="property-detail col">{{format(property.total_raw_amount) + 'đ'}}</p>
              </div>
              <div v-for="(properties, index) in property.properties" :key="index">
                <div v-for="(property_detail, index) in properties.property_detail" :key="index">
                  <div class="property-content row" >
                    <p class="property-title col-4">Đơn giá đất: </p>
                    <p class="property-detail col"><b>{{format(property_detail.price_land) + 'đ ' + '(' + property_detail.land_type_purpose_data.description + ')'}}</b></p>
                  </div>
                </div>
              </div>
              <div class="property-content row" v-if="property.asset_type_id === 39">
                <p class="property-title col-4">Đơn giá bình quân căn hộ: </p>
                <p class="property-detail col"><b>{{ format(property.room_details[0].unit_price)}}đ</b></p>
              </div>
            </div>
          </div>
            <div class="card-table">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Nguồn thông tin</h3>
                  <img class="img-dropdown" :class="show_source_info ? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="show_source_info = !show_source_info">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="show_source_info">
                <div class="property-content row">
                  <p class="property-title col-4">Loại giao dịch: </p>
                  <p class="property-detail color-blue col" v-if="property.transaction_type_id === 51">{{property.transaction_type.description}}</p>
                  <p class="property-detail color-orange col" v-if="property.transaction_type_id === 53">{{property.transaction_type.description}}</p>
                  <p class="property-detail color-purple col" v-if="property.transaction_type_id === 52">{{property.transaction_type.description}}</p>
                  <p class="property-detail color-green col" v-if="property.transaction_type_id === 54">{{property.transaction_type.description}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Người liên hệ: </p>
                  <p class="property-detail col">{{property.contact_person !== undefined && property.contact_person !== null ? property.contact_person : ''}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4"> Số điện thoại liên hệ: </p>
                  <p class="property-detail col">{{property.contact_phone !== undefined && property.contact_phone !== null ? property.contact_phone : ''}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Ngày đăng tin: </p>
                  <p class="property-detail col">{{property.public_date}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Ngày xác thực: </p>
                  <p class="property-detail col">{{formatDate(property.updated_at)}}</p>
                </div>
                <div class="property-content row">
                  <p class="property-title col-4">Nhân viên xác thực: </p>
                  <p class="property-detail col">{{property.created_by.name}}</p>
                </div>
              </div>
            </div>
        </div>
          <div class="container__print">
            <button class="btn btn-orange btn-orange--print mr-2" type="button" @click="print(property.id)">Tải xuống</button>
            <!-- <button class="btn btn-orange btn-orange--print" type="button" @click="openModalPrint(property.id)">In</button> -->
          </div>
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
	name: 'ModalMapDetail',
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
	methods: {
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
    color: #FAA831;
    font-weight: 600;
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
</style>
