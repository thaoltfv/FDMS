<template>
  <div class="container-fluid">
    <div class="contain-detail">
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title text-nowrap">Thông tin về hồ sơ thẩm định</h3>
            <img class="img-dropdown" :class="!showInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfo = !showInfo">
          </div>
        </div>
        <div class="card-body card-info" v-if="showInfo">
          <div class="d-grid">
            <div class="content-detail content-code">
              <p class="content-title">Mã hồ sơ:</p>
              <p class="content-name content-name__code">HSTD_{{form.id }}</p>
            </div>
            <div class="content-detail content-code">
              <p class="content-title">Mã nhiệm vụ Base:</p>
              <p class="content-name">{{form.ticket_num }}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Trạng thái:</p>
              <p class="content-name" v-if="form.status === 1">Nháp</p>
              <p class="content-name" v-if="form.status === 2">Mới</p>
              <p class="content-name" v-if="form.status === 3">Đang duyệt</p>
              <p class="content-name" v-if="form.status === 4">Đã duyệt</p>
              <p class="content-name" v-if="form.status === 5">Đã Hủy</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail content-code">
              <p class="content-title">Số CVĐ:</p>
              <p class="content-name">{{form.document_num }}</p>
            </div>
            <div class="content-detail content-code">
              <p class="content-title">Ngày CVĐ:</p>
              <p class="content-name">{{form.document_date }}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Thời điểm thẩm định:</p>
              <p class="content-name">{{form.appraise_date}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail content-code">
              <p class="content-title">Số chứng thư:</p>
              <p class="content-name">{{form.certificate_num }}</p>
            </div>
            <div class="content-detail content-code">
              <p class="content-title">Ngày chứng thư:</p>
              <p class="content-name">{{form.certificate_date }}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Mục đích thẩm định:</p>
              <p class="content-name">{{form.appraise_purpose.name}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail content-code" v-if="form.petitioner_name">
              <p class="content-title">Tên KH yêu cầu (trên Chứng thư):</p>
              <p class="content-name">{{form.petitioner_name }}</p>
            </div>
            <div class="content-detail content-code" v-if="form.petitioner_phone">
              <p class="content-title">Điện thoại:</p>
              <p class="content-name">{{form.petitioner_phone }}</p>
            </div>
            <div class="content-detail" v-if="form.petitioner_address">
              <p class="content-title">Địa chỉ:</p>
              <p class="content-name">{{form.petitioner_address}}</p>
            </div>
          </div>
          <div class="col-12" v-if="form.appraises && form.appraises.length > 0">
            <div class="container-asset">
              <h3 class="title-asset">Tài sản thẩm định</h3>
              <div class="row m-0 justify-content-center">
                <div class="col-12 col-lg-3 mt-2" v-for="asset in form.appraises" :key="asset.id">
                  <div class="container__property">
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Mã tài sản:</p>
                      <p class="content content__id">{{'TSTD_' + asset.appraise_id}}</p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Loại tài sản:</p>
                      <p class="content">{{ asset.asset_type.description }}</p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Loại đất</p>
                      <p class="content">{{asset.properties && asset.properties.length > 0 && asset.properties[0].property_detail && asset.properties[0].property_detail.length > 0 ? asset.properties[0].property_detail[0].land_type_purpose.acronym : ''}} {{asset.properties && asset.properties.length > 0 && asset.properties[0].property_detail && asset.properties[0].property_detail.length > 1 ? ', ' + asset.properties[0].property_detail[1].land_type_purpose.acronym : ''}}</p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Tổng diện tích:</p>
                      <p class="content">{{ asset.properties && asset.properties.length > 0 && asset.properties[0].appraise_land_sum_area ? asset.properties[0].appraise_land_sum_area : 0 }} m <sup>2</sup> </p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Người tạo:</p>
                      <p class="content">{{ asset.created_by.name }} </p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="mb-0 w-100 text-left">Mô tả: <span>{{asset.asset_type.description ? asset.asset_type.description + ',' : ''}} {{asset.doc_no ? 'Số tờ: ' + asset.doc_no + ',' : ''}} {{asset.land_no ? 'Số thửa: ' + asset.land_no + ',' : ''}} {{asset.street ? asset.street.name + ', ' : ''}} {{asset.ward ? asset.ward.name + ', ' : ''}} {{asset.district ? asset.district.name + ', ' : ''}} {{asset.province ? asset.province.name : ''}}</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="form.appraises && form.appraises.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title text-nowrap">Tổ thẩm định</h3>
            <img class="img-dropdown" :class="!showPerson? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showPerson = !showPerson">
          </div>
        </div>
        <div class="card-body card-info" v-if="showPerson">
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Thẩm định viên:</p>
              <p class="content-name">{{form.appraiser ? form.appraiser.name : ''}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Tổng giám đốc:</p>
              <p class="content-name">{{form.appraiser_manager ? form.appraiser_manager.name : ''}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">KT - Tổng giám đốc:</p>
              <p class="content-name">{{form.appraiser_confirm ? form.appraiser_confirm.name : ''}}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="form.appraises && form.appraises.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title text-nowrap">Cơ sở giá trị, cách tiếp cận và phương pháp thẩm định</h3>
            <img class="img-dropdown" :class="!showCard? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
          </div>
        </div>
        <div class="card-body card-info" v-if="showCard">
          <div class="d-grid d-grid--two">
            <div class="content-detail">
              <p class="content-title">Cơ sở giá trị của tài sản thẩm định giá:</p>
              <ul v-if="form.appraise_basis_property && form.appraise_basis_property.length > 0">
                <li class="content-name" v-for="basic in form.appraise_basis_property" :key="basic.id">
                  {{ basic.name }}
                </li>
              </ul>
            </div>
            <div class="content-detail">
              <p class="content-title">Nguyên tắc thẩm định:</p>
              <ul v-if="form.certificate_principle && form.certificate_principle.length > 0">
                <li class="content-name" v-for="principle in form.certificate_principle" :key="principle.id">
                  {{ principle.name }}
                </li>
              </ul>
            </div>
          </div>
          <div class="d-grid d-grid--two">
            <div class="content-detail">
              <p class="content-title">Cách tiếp cận:</p>
              <ul v-if="form.certificate_approach && form.certificate_approach.length > 0">
                <li class="content-name" v-for="approach in form.certificate_approach" :key="approach.id">
                  {{ approach.name }}
                </li>
              </ul>
            </div>
            <div class="content-detail">
              <p class="content-title">Phương pháp sử dụng:</p>
              <ul v-if="form.appraise_method_used && form.appraise_method_used.length > 0">
                <li class="content-name" v-for="method in form.appraise_method_used" :key="method.id">
                  {{ method.name }}
                </li>
              </ul>
            </div>
          </div>
          <div>
            <div class="content-detail">
              <p class="content-title">Giả thiết và giả thiết đặc biệt:</p>
              <p class="content-name">{{form.document_description}}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="form.appraises && form.appraises.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Căn cứ pháp lý thẩm định giá</h3>
            <img class="img-dropdown" :class="!showCardAsset ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardAsset = !showCardAsset">
          </div>
        </div>
        <div class="card-body card-info" v-show="showCardAsset">
          <div class="container-content__detail row align-items-center">
            <div class="container-content__title col-3 mb-0">Văn bản pháp luật về thẩm định giá</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalExpertise">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{form.legal_documents_on_valuation.length}} văn bản
              </div>
            </div>
          </div>
          <div class="container-content__detail row align-items-center">
            <div class="container-content__title col-3 mb-0">Văn bản pháp luật về đất đai</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalLands">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{form.legal_documents_on_land.length}} văn bản
              </div>
            </div>
          </div>
          <div class="container-content__detail row align-items-center">
            <div class="container-content__title col-3 mb-0">Văn bản pháp luật về xây dựng</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalConstructs">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{form.legal_documents_on_construction.length}} văn bản
              </div>
            </div>
          </div>
          <div class="container-content__detail row align-items-center mb-0">
            <div class="container-content__title col-3 mb-0">Văn bản pháp luật của địa phương</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalLocal">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{form.legal_documents_on_local.length}} văn bản
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="form.appraises && form.appraises.length > 0 && form.construction_company.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Đơn vị xây dựng</h3>
            <img class="img-dropdown" :class="!showCardCompany ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardCompany = !showCardCompany">
          </div>
        </div>
        <div class="card-body card-info" v-show="showCardCompany">
          <div class="container-content__detail row align-items-center mb-0">
            <div class="container-content__title col-3 mb-0">Đơn vị xây dựng</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalCompany">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{constructionLength}} tài sản
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card" v-if="form.appraises && form.appraises.length > 0 && propertyAssets.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title">Chi tiết tài sản so sánh</h3>
            </div>
            <img class="img-dropdown" :class="!showCardProperty ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardProperty = !showCardProperty">
          </div>
        </div>
        <div class="card-body card-info" v-show="showCardProperty">
          <div class="row">
            <!-- <div class="col-12 col-lg-3 mt-2" v-for="(asset, index) in propertyAssets" :key="index">
              <div class="container__property">
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Tài sản:</p>
                  <p class="content content__id">{{asset.migrate_status + '_' + asset.id}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Phiên bản:</p>
                  <p class="content">{{asset.version ? asset.version : ''}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Loại giao dịch:</p>
                  <p class="content" :class="asset.transaction_type_id === 51? 'color__blue': asset.transaction_type_id === 53 ? 'color__orange' : asset.transaction_type_id === 52 ? 'color__purple': 'color__green'">{{asset.transaction_type_description}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Vị trí:</p>
                  <p class="content">{{asset.front_side === 0 ? 'Hẻm' : asset.front_side === 1? 'Mặt tiền' : '' }}</p>
                </div>
                <div class="property__detail d-flex justify-content-between" v-if="asset.front_side === 0">
                  <p class="name">Bề rộng hẻm:</p>
                  <p class="content">{{asset.main_road_length ? formatArea(asset.main_road_length) + ' m' : '' }}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Tổng diện tích:</p>
                  <p class="content">{{formatArea(asset.total_area)}} m<sup>2</sup></p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Giá trị ước tính:</p>
                  <p class="content">{{format(asset.total_estimate_amount)}} đ</p>
                </div>
                <div v-for="(landTypePrice, index) in asset.land_type_purpose_price" :key="'landTypePrice' + index">
                  <div class="property__detail d-flex justify-content-between" v-for="(land_type, index) in type_purposes" :key="'land_type' + index" v-if="landTypePrice.id === land_type.id">
                    <p class="name">Loại đất:</p>
                    <p class="content">{{land_type.description}}</p>
                  </div>
                  <div class="property__detail d-flex justify-content-between">
                    <p class="name">Đơn giá:</p>
                    <p class="content">{{format(landTypePrice.price_land)}} đ</p>
                  </div>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="mb-0 w-100 text-left">Địa chỉ: <span>{{asset.full_address}}</span></p>
                </div>
                <p class="popup-link" @click="handleDetail(asset, index)">Xem chi tiết</p>
              </div>
            </div> -->
            <div class="col-4 d-flex mt-3" v-for="data in propertyAssets" :key="data.id" >
              <button class="btn btn-orange" style="width: auto" type="button" @click="handleMap(data)">Sơ đồ vị trí tài sản thẩm định TSTD_{{data.appraise_id}}</button>
            </div>
          </div>

        </div>
      </div>
      <div class="card" v-if="form.appraises && form.appraises.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Yếu tố so sánh</h3>
            <img class="img-dropdown" :class="!showFactor ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showFactor = !showFactor">
          </div>
        </div>
        <div class="card-body card-info" v-show="showFactor">
          <div class="container-content__detail row align-items-center mb-0">
            <div class="container-content__title col-3 mb-0">Yếu tố so sánh</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalSelection">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{comparisonLength}} tài sản
              </div>
            </div>
          </div>
        </div>
      </div>
      <DocumentAppraisal
        v-if="form.appraises && form.appraises.length > 0"
        ref="documentAppraisal"
        :idData="form.id"
        :status="form.status"
        :dataForm="form"
        :checkRole="checkRole"
      />
      <OtherPictureDetail
        v-if="form.appraises && form.appraises.length > 0"
        :files="form.other_documents"
        :data="form"
        @handleChangeFile="handleChangeFile"
      />
    </div>
    <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
      <div class="d-md-flex d-block button-contain ">

        <button v-if="form.status === 3 && accept ? true : false " class="btn btn-white btn-orange" @click.prevent="handleAccept">
            <img class="img" src="../../assets/icons/ic_done.svg" alt="edit">
            Đồng ý phê duyệt
        </button>

        <button v-if="form.status === 3 && accept ? true : false " class="btn btn-white" @click.prevent="handleDenined">
            <img class="img" src="../../assets/icons/ic_cancel-1.svg" alt="edit">
            Từ chối phê duyệt
        </button>

         <button v-if="form.status === 2 && accept ? true : false " class="btn btn-white btn-orange" @click.prevent="handleSendRequire">
            <img class="img" src="../../assets/icons/ic_done.svg" alt="edit">
            Gửi phê duyệt
        </button>

        <button v-if="(form.status === 1 || form.status === 2) && edit && checkRole ? true : false " class="btn btn-white" @click.prevent="handleEdit(form.id)">
          <img class="img" src="../../assets/icons/ic_edit.svg" alt="edit">
          Chỉnh sửa
        </button>
        <!------------------------------- Trở về------------------------------->
        <button v-if="form.status !== 1 && form.status !== 2" class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">Trở về
        </button>

        <!-- <b-button-group v-if="form.status === 1 || form.status === 2">
          <button style="margin-right: 2px" class="btn btn-white" @click="onCancel" type="button">
            <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">Trở về
          </button>
          <b-dropdown class="btn_dropdown" right>
            <b-dropdown-item @click="handleCancelProperty(idData)">
              <button style="width:100%" v-if="form.status === 2 && accept ? true : false " class="btn btn-white" @click.prevent="handleCancelCertificate">
                <img class="img" src="../../assets/icons/ic_destroy.svg" alt="edit">
                Hủy hồ sơ
              </button>
            </b-dropdown-item>
          </b-dropdown>
        </b-button-group> -->

      </div>
      <b-button-group style="margin-left: 15px" class="btn_group" v-if="form.status === 1 || form.status === 2">
          <button style="margin-right: 2px" class="btn btn-white" @click="onCancel" type="button">
            <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">Trở về
          </button>
          <b-dropdown class="btn_dropdown" right dropup>
            <b-dropdown-item v-if="(form.status === 2 || form.status === 1) && edit ? true : false" @click.prevent="handleCancelCertificate">
              <div class="div_item_dropdown">
                <img style="height: 20px" class="img" src="../../assets/icons/ic_destroy.svg" alt="edit">
                  Hủy hồ sơ
              </div>
            </b-dropdown-item>
          </b-dropdown>
      </b-button-group>
    </div>
    <ModalDocumentsDetail
      v-if="openModalDocuments"
      @cancel="cancelData"
      :documents="documents"
      :data="data"
      :title="title"
    />
    <ModalConstructionUnitDetail
      v-if="openModalDocumentsCompany"
      @cancel="cancelDataCompany"
      :documents="docConstruction"
      :title="title"
    />
    <ModalCompatatorSelectionDetail
      v-if="openModalDocumentsSelection"
      @cancel="cancelDataSelection"
      :documents="documents"
      :title="title"
    />
    <ModalInfoDetail
      v-if="open_detail"
      @cancel="open_detail = false"
      :property="property"
      :pic="pic"
    />
    <ModalMapAssets
      v-if="openModalMapAsset"
      :assets="propertyCompare"
      :assetArray="propertyExpertise"
      @cancel="openModalMapAsset = false"
    />
     <ModalNotificationCertificate
      v-if="openNotification"
      @cancel="handleCancel"
      v-bind:notification="message"
      @action="handleAction"
    />
     <ModalNotificationCertificate
      v-if="openNotificationDenined"
      @cancel="openNotificationDenined = false"
      v-bind:notification="message"
      @action="handleActionDenined"
    />
  </div>
</template>

<script>
import ModalCompatatorSelectionDetail from '@/pages/appraisal/components/modals/ModalCompatatorSelectionDetail'
import ModalNotificationCertificate from '@/components/Modal/ModalNotificationCertificate'
import ModalConstructionUnitDetail from './components/modals/ModalConstructionUnitDetail'
import ModalInfoDetail from '@/pages/appraisal/components/modals/ModalInfoDetail'
import ModalMapAssets from '@/pages/appraisal/components/modals/ModalMapAssets'
import DocumentAppraisal from '@/pages/appraisal/components/DocumentAppraisal'
import {BDropdown, BDropdownItem, BTooltip, BButtonGroup} from 'bootstrap-vue'
import ModalDocumentsDetail from './components/modals/ModalDocumentsDetail'
import OtherPictureDetail from './components/OtherPictureDetail'
import AppraiseData from '@/models/CertificateAssetData'
import Certificate from '@/models/Certificate'
import WareHouse from '@/models/WareHouse'
export default {
	name: 'Detail',
	data () {
		return {
			form: {},
			checkRole: false,
			showInfo: true,
			showPerson: true,
			showCardAsset: false,
			showCardCompany: false,
			showCard: true,
			showFactor: false,
			showCardProperty: false,
			open_detail: false,
			data: '',
			title: null,
			documents: [],
			openModalDocuments: false,
			openModalDocumentsCompany: false,
			openModalDocumentsSelection: false,
			propertyAssets: [],
			property: null,
			pic: [],
			openModalMapAsset: false,
			type_purposes: [],
			propertyExpertise: [],
			propertyCompare: [],
			docConstruction: [],
			docComparison: [],
			constructionLength: 0,
			comparisonLength: 0,
			openNotification: false,
			openNotificationDenined: false,
			message: '',
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			cancel_certificate: false
		}
	},
	async created () {
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		permission.forEach(value => {
			if (value === 'VIEW_PRICE') {
				this.view = true
			}
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_PRICE') {
				this.accept = true
			}
		})
		if ('id' in this.$route.query && this.$route.name === 'appraisal.detail') {
			if (this.$route.meta['detail']) {
				this.form = Object.assign(this.form, {
					...this.$route.meta['detail']
				})
				this.getProfiles()
				if (this.form.appraises && this.form.appraises.length > 0) {
					this.$nextTick(() => {
						this.$refs.documentAppraisal.getDataAppraises(this.form.id)
					})
					this.getAppraisersData(this.form.appraises)
				}
			} else {
				this.$router.push({name: 'page-not-found'})
			}
		} else {
		}
		permission.forEach(value => {
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
		})
	},
	components: {
		ModalCompatatorSelectionDetail,
		ModalNotificationCertificate,
		ModalConstructionUnitDetail,
		ModalDocumentsDetail,
		OtherPictureDetail,
		DocumentAppraisal,
		ModalInfoDetail,
		ModalMapAssets,
		'b-dropdown-item': BDropdownItem,
		'b-button-group': BButtonGroup,
		'b-dropdown': BDropdown,
		'b-tooltip': BTooltip
	},
	methods: {
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
			if (this.form.created_by && profile.data.user.roles[0].pivot.model_id === this.form.created_by.id) {
				this.checkRole = true
			}
		},
		handleChangeFile (files) {
			this.form.other_documents = files
		},
		async handleActionDenined () {
			if (this.form.status === 3) {
				// denined change status 3 ---> 2
				let status = 2
				const res = await AppraiseData.updateStatusCertificate(this.form.id, status)
				if (res.data) {
					await this.$toast.open({
						message: 'Từ chối phê duyệt thành công',
						type: 'success',
						position: 'top-right',
						duration: 3000
					})
					this.openNotificationDenined = await false
					await this.$router.push({name: 'appraisal.index'}).catch(_ => {})
				}
			}
		},
		async handleAction () {
			if (this.form.status === 2 && !this.cancel_certificate) {
				// change status 2 --> 3
				let status = 3
				const res = await AppraiseData.updateStatusCertificate(this.form.id, status)
				if (res.data === true) {
					await this.$toast.open({
						message: 'Gửi phê duyệt thành công',
						type: 'success',
						position: 'top-right',
						duration: 3000
					})
					this.openNotification = await false
					await this.$router.push({name: 'appraisal.index'}).catch(_ => {})
				}
			} else if (this.form.status === 3 && !this.cancel_certificate) {
				// change status 3 --> 4
				let status = 4
				const res = await AppraiseData.updateStatusCertificate(this.form.id, status)
				if (res.data === true) {
					await this.$toast.open({
						message: 'Xác nhận hồ sơ thành công',
						type: 'success',
						position: 'top-right',
						duration: 3000
					})
					this.openNotification = await false
					await this.$router.push({name: 'appraisal.index'}).catch(_ => {})
				}
			} else if (this.cancel_certificate) {
				// change status 2 --> 5
				let status = 5
				const res = await AppraiseData.updateStatusCertificate(this.form.id, status)
				if (res.data === true) {
					await this.$toast.open({
						message: 'Hủy hồ sơ thành công',
						type: 'success',
						position: 'top-right',
						duration: 3000
					})
					this.openNotification = await false
					await this.$router.push({name: 'appraisal.index'}).catch(_ => {})
				}
				this.cancel_certificate = await false
			}
		},
		handleCancel () {
			this.openNotification = false
			if (this.cancel_certificate) {
				this.cancel_certificate = false
			}
		},
		handleCancelCertificate () {
			this.openNotification = true
			this.cancel_certificate = true
			this.message = 'Bạn có muốn hủy hồ sơ này?'
		},
		handleAccept () {
			this.openNotification = true
			this.message = 'Bạn có muốn duyệt hồ sơ này?'
		},
		handleDenined () {
			this.openNotificationDenined = true
			this.message = 'Bạn có muốn từ chối hồ sơ này'
		},
		handleSendRequire () {
			this.openNotification = true
			this.message = 'Bạn có muốn gửi duyệt hồ sơ này'
		},
		handleMap (data) {
			if (this.propertyAssets.length > 0) {
				this.propertyExpertise = data
				this.propertyCompare = data.asset_general
				this.openModalMapAsset = true
			} else {
				this.$toast.open({
					message: 'Hiện tại không chưa có danh sách tài sản so sánh',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async getAppraiseById (id, version) {
			const res = await AppraiseData.getAppraiseAssetID(id, version)
			this.getPropertyAssets(res.data)
		},
		getPropertyAssets (data) {
			if (data.asset_general && data.asset_general.length > 0) {
				// data.asset.forEach(propertyAsset => {
				//   if (this.propertyAssets.length > 0) {
				//     const id = this.propertyAssets.find(item => propertyAsset.id === item.id)
				//     if (id === undefined || id === null) {
				//       this.propertyAssets.push(propertyAsset)
				//     }
				//   } else {
				//     this.propertyAssets.push(propertyAsset)
				//   }
				// })
				this.propertyAssets.push(data)
			}
		},
		async getAppraisersData (data) {
			this.form.construction_company = []
			this.form.comparison_factor = []
			if (data.length > 0) {
				for (const item of data) {
					if ((typeof item !== 'undefined') && (typeof item.version !== 'undefined')) {
						// get data construction
						if (item.tangible_assets && item.tangible_assets.length > 0) {
							await this.getDataConstruction(item)
						}
						// get data comparison
						await this.getComparisonData(item.comparison_factor_custom, item.id, item.appraise_id)
						await this.getAppraiseById(item.id)
					}
				}
			}
		},
		// get data construction
		async getDataConstruction (dataAppraise) {
			let constructArray = []
			if (typeof dataAppraise !== 'undefined') {
				await dataAppraise.construction_company.forEach(data => {
					constructArray.push(data)
				})
				await this.form.construction_company.push({
					id: dataAppraise.appraise_id,
					table: constructArray
				})
			}
			if (this.form.construction_company && this.form.construction_company.length > 0) {
				this.constructionLength = this.form.construction_company.length
			}
		},
		// get data comparison
		async getComparisonData (dataCompare, newID, oldID) {
			let arrayCompare = []
			if (dataCompare && dataCompare[newID] && dataCompare[newID].length > 0) {
				dataCompare[newID].forEach(data => {
					arrayCompare.push({name: data.label})
				})
			}
			if (typeof dataCompare !== 'undefined') {
				await this.form.comparison_factor.push({
					id: oldID,
					table: arrayCompare
				})
			}
			if (this.form.comparison_factor && this.form.comparison_factor.length > 0) {
				this.comparisonLength = this.form.comparison_factor.length
			}
		},
		handleOpenModalExpertise () {
			this.title = 'Văn bản pháp luật về thẩm định giá'
			this.documents = this.form.legal_documents_on_valuation
			this.data = 'valuation'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalLands () {
			this.title = 'Văn bản pháp luật về đất đai'
			this.documents = this.form.legal_documents_on_land
			this.data = 'land'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalConstructs () {
			this.title = 'Văn bản pháp luật về xây dựng'
			this.documents = this.form.legal_documents_on_construction
			this.data = 'construct'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalLocal () {
			this.title = 'Văn bản pháp luật của địa phương'
			this.documents = this.form.legal_documents_on_local
			this.data = 'local'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalCompany () {
			this.title = 'Đơn giá xây dựng'
			this.docConstruction = this.form.construction_company
			this.openModalDocumentsCompany = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalSelection () {
			this.title = 'Yếu tố so sánh'
			this.documents = this.form.comparison_factor
			this.openModalDocumentsSelection = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		cancelDataCompany () {
			this.openModalDocumentsCompany = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		cancelDataSelection () {
			this.openModalDocumentsSelection = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		cancelData () {
			this.openModalDocuments = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		onCancel () {
			return this.$router.push({name: 'appraisal.index'})
		},
		async handleDetail (property) {
			await this.getAssetGeneralDetail(property.id)
			this.pic = property.pic
			this.open_detail = true
		},
		async getAssetGeneralDetail (id) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(id)
			this.property = resp.data
			this.isSubmit = false
		},
		async getDictionary () {
			try {
				const resp = await WareHouse.getDictionaries()
				this.type_purposes = [...resp.data.loai_dat_chi_tiet]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async handleEdit (id) {
			this.$router.push({
				name: 'appraisal.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		const certificate = await Certificate.find(to.query['id'])
		to.meta['detail'] = certificate.data
		return next()
	},
	beforeMount () {
		this.getDictionary()
	}
}
</script>
<style lang="css">
.vue-tabs .nav-tabs>li.active>a, .vue-tabs .nav-tabs>li.active>a:hover, .vue-tabs .nav-tabs>li.active>a:focus {
    background-color: #FAA831;
    font-weight: 900;
}
.vue-tabs .nav-tabs>li>a {
  margin-right: 10px;
}
</style>
<style scoped lang="scss">
  .contain-detail {
      margin-bottom: 80px;
    @media (max-width: 767px) {
      margin-bottom: 145px;
    }
  }
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      @media (max-width: 418px) {
        padding: 15px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 418px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
        &-highlight {
          color: #333333;
          background: rgba(252, 194, 114, 0.53);
          text-align: center;
          padding: 10px 0;
          border-radius: 2px;
        }
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .content{
    &-detail{
      p {
        padding-right: 10px;
        &:nth-last-child(1) {
          padding-right: 0;
        }
      }
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
  .d-grid{
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-column-gap: 8.9%;
    &--two {
      grid-template-columns: 1fr 1fr;
    }
    @media (max-width: 767px) {
      grid-template-columns: 1fr;
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .container-content {
    &__detail {
      margin-bottom: 20px;
    }
    &__title {
      font-size: 0.875rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #333333;
    }
  }
  .btn-footer {
    background: #FFFFFF;
    padding: 20px 30px;
    position: fixed;
    left: 0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    bottom: 0;
    right: 0;
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
        &__detail{

          color: #000000;
          margin-bottom: 5px;
          .name, .content{
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
            &__id{
              color: #FAA831;
            }
          }
        }
      }
    }
    &-asset {
      margin-top: 10px;
      border: 1px solid;
      padding: 20px;
    }
  }
  .title-asset {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    text-transform: uppercase;
  }
  .popup{
    &-link {
      text-align: right;
      color: #F28C1C !important;
      font-weight: 600;
      text-decoration-line: underline;
      cursor: pointer;
      margin-bottom: 0 !important;
    }
  }
  .btn_dropdown {
    box-shadow: 0 1px 4px rgba(0,0,0,0.25);
    border:white;
    border-radius: 5px;
  }
  .div_item_dropdown {
    padding: 0px 22%;
  }

</style>
