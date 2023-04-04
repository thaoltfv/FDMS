<template>
  <div class="row mt-3 content_detail_asset" :key="render_tab_3">
    <div class="col-12" v-if="data.tangible_assets && data.tangible_assets.length > 0 &&  data.tangible_assets[0].construction_company.length > 0">
      <div class="col-12 contruction_block">
        <div class="d-flex align-items-center justify-content-between sub_header_title">
            <span class="label">
            Đơn giá xây dựng lựa chọn
            </span>
            <button v-if="editAsset" style="color: #617F9E" class="btn  btn-ghost" type="button" @click="handleEditContruction" >
              <img src="@/assets/icons/ic_edit_2.svg" style="margin-right: 8px" alt="search"/>Chỉnh sửa
            </button>
        </div>
        <div class="main-wrapper">
          <div class="responsive-table">
            <table class="table_detail_contruction color_content">
              <thead>
                <tr>
                  <th>Tên tài sản</th>
                  <th>Đvt</th>
                  <th v-for="(company) in form.tangible_assets[0].construction_company" :key="company.id" >{{company.name}}</th>
                  <th v-for="(item, index) in constructionPriceType" :key="index">
                      <label :for="item.slug">{{item.name}}</label>
                      <label class="input-checkbox">
                        <input
                          class="input"
                          :class="{'disable_input': !editAsset}"
                          type="checkbox"
                          :id="item.slug"
                          :value="item.slug"
                          :disabled="!editAsset"
                          v-model="constructionPriceTypeSelected"
                          @change="selectConstructionPriceType($event)"
                        />
                        <span class="check-mark"/>
                      </label>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(tangibleAsset, indexTangible) in form.tangible_assets" :key="tangibleAsset.id">
                  <td>{{tangibleAsset ? tangibleAsset.tangible_name : ""}}</td>
                  <td>m<sup>2</sup></td>
                  <td v-for="(company, index) in tangibleAsset.construction_company" :key="company.id">
                    <InputCurrency
											:disabled="!editAsset"
                      v-model="company.unit_price_m2"
                      vid="unit_price_m2"
                      class="label-none"
                      label="Đơn giá công ty"
                      @change="changeContructionCompany($event,index)"
                    />
                  </td>
                  <td>{{total_average ? formatNumber(total_average[indexTangible]) : 0}} đ</td>
                  <td>
                    <InputCurrency
											:disabled="!editAsset"
                      :key="render_input"
                      :defaultValue="0"
                      v-model="tangibleAsset.total_desicion_average"
                      vid="total_desicion_average"
                      class="label-none"
                      label="Đơn giá công ty"
                      @change="changeDesicionAverage($event,indexTangible)"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
      <div class="col-12 contruction_block">
        <div class="d-flex align-items-center sub_header_title">
            <span class="label">
            Xác định chất lượng còn lại
            </span>
        </div>
        <Tabs class="tab_contruction" :navAuto="true">
          <TabItem name="Chất lượng còn lại lựa chọn">
            <div class="main-wrapper">
              <div class="responsive-table">
                <table class="table_contruction_pp1 color_content">
                    <thead>
                      <tr>
                        <th>Tên tài sản</th>
                        <th class="clcl_width">Năm sử dụng</th>
                        <th v-for="(item, index) in constructionRemainQuality" :key="index" class="clcl_width">
                            <label :for="item.slug">{{item.name}}</label>
                            <label class="input-checkbox">
                              <input
                                class="input"
                                :class="{'disable_input': !editAsset}"
                                type="checkbox"
                                :id="item.slug"
                                :value="item.slug"
                                :disabled="!editAsset"
                                v-model="constructionRemainQualitySelected"
                                @change="selectConstructionRemainQuality($event)"
                              />
                              <span class="check-mark"/>
                            </label>
                        </th>
                        <th>CLCL lựa chọn</th>
                      </tr>
                    </thead>
                     <tbody>
                      <tr v-for="(tangibleAsset, indexTangible) in form.tangible_assets" :key="`CL-${tangibleAsset.id}`">
                        <td>{{ tangibleAsset ? tangibleAsset.tangible_name : ""}}</td>
                        <td>{{ tangibleAsset ? tangibleAsset.start_using_year : ""}}</td>
                        <td>{{ tangibleAsset ? tangibleAsset.remaining_quality : 0}}</td>
                        <td>{{ total[indexTangible] ? total[indexTangible] : 0 }}</td>
                        <td>{{remaining_quality_average[indexTangible]}}</td>
                        <td>{{remaining_quality_choosing[indexTangible]}}</td>
                      </tr>
                    </tbody>
                  </table>
              </div>
            </div>
          </TabItem>
          <TabItem name="Phương pháp tuổi đời(PP1)">
            <div class="main-wrapper">
              <div class="responsive-table">
                <table class="table_contruction_pp1 color_content">
                  <thead>
                    <tr>
                      <th>Tên tài sản</th>
                      <th>Năm sử dụng</th>
                      <th>Thời gian đã sử dụng</th>
                      <th>Niên hạn theo quy định</th>
                      <th>CLCL(%)</th>
                    </tr>
                  </thead>
                    <tbody>
                    <tr v-for="(tangibleAsset) in form.tangible_assets" :key="`PP1-${tangibleAsset.id}`">
                      <td>{{ tangibleAsset ? tangibleAsset.tangible_name : ""}}</td>
                      <td>{{ tangibleAsset ? tangibleAsset.start_using_year : ""}}</td>
                      <td>{{ tangibleAsset ? currentYear - tangibleAsset.start_using_year : ""}}</td>
                      <td>{{ tangibleAsset ? tangibleAsset.duration : ""}}</td>
                      <td>{{ tangibleAsset ? tangibleAsset.remaining_quality : ""}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </TabItem>
          <TabItem name="Phương pháp chuyên gia(PP2)">
            <div class="main-wrapper">
              <div class="responsive-table">
                <table class="table_contruction_pp2 color_content" v-if="form.comparison_tangible_factor && form.comparison_tangible_factor">
                    <thead>
                      <tr>
                        <th rowspan="4">Tên tài sản</th>
                      </tr>
                      <tr>
                        <th colspan="10">Phần kết cấu chính (%)</th>
                        <th rowspan="2">CLCL (%)</th>
                      </tr>
                      <tr>
                        <th colspan="2">Móng, cột</th>
                        <th colspan="2">Tường</th>
                        <th colspan="2">Nền, sàn</th>
                        <th colspan="2">Kết cấu mái</th>
                        <th colspan="2">Mái</th>
                      </tr>
                      <tr>
                        <th>p</th>
                        <th>h</th>
                        <th>p</th>
                        <th>h</th>
                        <th>p</th>
                        <th>h</th>
                        <th>p</th>
                        <th>h</th>
                        <th>p</th>
                        <th>h</th>
                        <th colspan="2">H= Σ ph / Σ p</th>
                      </tr>

                    </thead>
                     <tbody>
                      <tr v-for="(tangibleAsset, indexTangible) in form.tangible_assets" :key="`PP2-${tangibleAsset.id}`">
                        <td>{{ tangibleAsset ? tangibleAsset.tangible_name : ""}}</td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="test"
                            vid="p1"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeP1($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.p1"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="h1"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeH1($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.h1"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeP2($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.p2"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="h2"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeH2($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.h2"
                          />
                        </td>
                        <td>
                           <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="p3"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeP3($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.p3"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="h3"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeH3($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.h3"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="d4"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeP4($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.d4"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="h4"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeH4($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.h4"
                          />
                        </td>
                        <td>
                          <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="p5"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeP5($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.p5"
                          />
                        </td>
                        <td>
                         <InputNumberNegative
														:disabled="!editAsset"
                            class="label-none"
                            label="tỷ lệ"
                            vid="h5"
                            :min="-10000"
                            :text_center="true"
                            :sufix="false"
                            @change="changeH5($event, indexTangible)"
                            v-model="tangibleAsset.comparison_tangible_factor.h5"
                          />
                        </td>
                        <td>{{ total[indexTangible] ? total[indexTangible] : 0 }}</td>
                      </tr>
                    </tbody>
                  </table>
              </div>
            </div>
          </TabItem>
        </Tabs>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center sub_header_title">
            <span class="label">
            Nhà cửa, vật kiến trúc
            </span>
        </div>
          <div class="main-wrapper">
            <div class="responsive-table">
            <table class="table_contruction_result color_content">
                <thead>
                  <tr>
                    <th>Tên tài sản</th>
                    <th class="clcl_width">ĐVT</th>
                    <th class="clcl_width">Số lượng</th>
                    <th class="clcl_width">CLCL(%)</th>
                    <th class="clcl_width">Đơn giá</th>
                    <th>Thành tiền</th>
                  </tr>
                </thead>
                  <tbody>
                    <tr v-for="(tangibleAsset, indexTangible) in form.tangible_assets" :key="`result-${tangibleAsset.id}`">
                      <td>{{ tangibleAsset ? tangibleAsset.tangible_name : ""}}</td>
                      <td>m<sup>2</sup></td>
                      <td>{{ tangibleAsset ? tangibleAsset.total_construction_base : ""}}</td>
                      <td>{{remaining_quality_choosing[indexTangible]}}</td>
                      <td>{{remaining_building_price_choosing && remaining_building_price_choosing.length > 0 ? formatNumber(remaining_building_price_choosing[indexTangible]) : 0}} đ</td>
                      <td>{{formatNumber(remaining_building_price[indexTangible])}} đ</td>
                    </tr>
                    <tr>
                      <td class="color_tr" colspan="5"><strong>TỔNG CỘNG</strong></td>
                      <td class="color_tr"><strong>{{formatNumber(total_remaining_building_price)}} đ</strong></td>
                    </tr>
                </tbody>
              </table>
          </div>
        </div>
      </div>

    </div>
    <div v-else class="col-12">
      <div class="infor-box">
          <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
          </svg>
          Tài sản thẩm định là đất trống nên không cần khai báo thông tin công trình xây dựng
        </div>
    </div>
    <div class="btn-footer d-md-flex d-block justify-content-end align-items-center w-100">
      <div class="d-md-flex d-block">
        <button  @click="onCancel" class="btn btn-white text-nowrap" >
          <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Thoát
        </button>
        <button v-if="editAsset" :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="handleSaveTab3">
          <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
        </button>
      </div>
    </div>
      <ModalListContruction
        :key="contruction_key"
        v-if="openListContruction"
        @cancel="openListContruction = false"
        :listDocument="constructions"
        :dataContruction="contructionSelected"
        :title="title"
        :idData="idData"
        :edit="edit"
        :status="status"
        :checkRole="checkRole"
        :editAsset="editAsset"
        @action="saveContruction"
      />
  </div>
</template>

<script>
import InputNumberNegative from '@/components/Form/InputNumberNegative'
import InputNumberNew from '@/components/Form/InputNumberNew'
import InputCurrency from '@/components/Form/InputCurrency'
import InputArea from '@/components/Form/InputArea'
import InputLengthArea from '@/components/Form/InputLengthArea'
import ModalListContruction from './ModalListContruction'
import { Tabs, TabItem } from 'vue-material-tabs'
import CertificateAsset from '@/models/CertificateAsset'
import Vue from 'vue'
import Icon from 'buefy'
Vue.use(Icon)
export default {
	name: 'tab_contruction',
	props: ['data', 'constructions', 'construction_company_ids', 'idData', 'edit', 'status', 'checkRole', 'editAsset', 'constructionRemainQuality', 'constructionPriceType'],
	components: {
		InputLengthArea,
		InputArea,
		Tabs,
		TabItem,
		InputCurrency,
		InputNumberNew,
		InputNumberNegative,
		ModalListContruction
	},
	computed: {
	},
	data () {
		return {
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			render_input: 3211234,
			currentYear: new Date().getFullYear(),
			total_desicion_average: 0,
			total: [],
			total_average: [],
			isSubmit: false,
			construction_company: [],
			comparison_tangible_factor: [],
			remaining_quality_average: [],
			remaining_quality_choosing: [],
			remaining_building_price: [],
			remaining_building_price_choosing: [],
			total_remaining_building_price: 0,
			openListContruction: false,
			contruction_key: 430,
			render_tab_3: 6230,
			contructionSelected: this.construction_company_ids ? JSON.parse(JSON.stringify(this.construction_company_ids)) : [],
			title: 'Lựa chọn đơn vị xây dựng',
			constructionRemainQualitySelected: [],
			constructionPriceTypeSelected: []
		}
	},
	watch: {
		constructionPriceTypeSelected: {
			deep: true,
			handler () {
				this.average()
			}

		},
		constructionRemainQualitySelected: {
			deep: true,
			handler (newValue) {
				this.remaining_quality_choosing = []
				this.setRemainQualityRate(newValue)
			}
		},
		remaining_quality_choosing: {
			deep: true,
			handler () {
				this.average()
			}
		}
	},
	async mounted () {
		this.getTotalMounted()
		this.average()
		let constructionDefault = this.constructionRemainQuality.find(i => i.is_defaults === true)
		if (constructionDefault) {
			this.constructionRemainQualitySelected.push(constructionDefault.slug)
		}
		let constructionPriceTypeDefault = this.constructionPriceType.find(i => i.is_defaults === true)
		if (constructionPriceTypeDefault) {
			this.constructionPriceTypeSelected.push(constructionPriceTypeDefault.slug)
		}
	},
	beforeUpdate () {
	},
	methods: {
		setRemainQualityRate (remainQualitySelected) {
			let slug = ''
			if (remainQualitySelected.length > 0) {
				slug = remainQualitySelected[0]
			}
			if (slug === 'tuoi-doi') {
				this.form.tangible_assets.forEach(item => {
					this.remaining_quality_choosing.push(item.remaining_quality)
				})
			} else if (slug === 'chuyen-gia') {
				this.remaining_quality_choosing = this.total
			} else {
				this.remaining_quality_choosing = this.remaining_quality_average
			}
		},
		selectConstructionPriceType (event) {
			// console.log(event)
			this.constructionPriceTypeSelected = []
			let slug = event.target.id
			let checked = event.target.checked
			if (checked) {
				this.constructionPriceTypeSelected.push(slug)
			}
		},
		selectConstructionRemainQuality (event) {
			// console.log(event)
			this.constructionRemainQualitySelected = []
			let slug = event.target.id
			let checked = event.target.checked
			if (checked) {
				this.constructionRemainQualitySelected.push(slug)
			}
		},
		getTotalMounted () {
			this.total = []
			if (typeof this.form.tangible_assets !== 'undefined' && this.form.tangible_assets.length > 0) {
				this.form.tangible_assets.forEach(item => {
					let p1 = item.comparison_tangible_factor.p1
					let p2 = item.comparison_tangible_factor.p2
					let p3 = item.comparison_tangible_factor.p3
					let d4 = item.comparison_tangible_factor.d4
					let p5 = item.comparison_tangible_factor.p5
					let h1 = item.comparison_tangible_factor.h1
					let h2 = item.comparison_tangible_factor.h2
					let h3 = item.comparison_tangible_factor.h3
					let h4 = item.comparison_tangible_factor.h4
					let h5 = item.comparison_tangible_factor.h5
					let total_factor = 0
					if (p1 + p2 + p3 + d4 + p5 !== 0) {
						total_factor = Math.round(parseFloat((p1 * h1 + p2 * h2 + p3 * h3 + d4 * h4 + p5 * h5) / (p1 + p2 + p3 + d4 + p5)).toFixed(1))
						this.total.push(total_factor)
					} else {
						this.total.push(0)
					}
					this.remaining_quality_average.push(Math.round((item.remaining_quality + +total_factor) / 2))
				})
			}
		},
		average () {
			this.total_average = []
			this.remaining_building_price = []
			this.remaining_building_price_choosing = []
			this.total_remaining_building_price = 0
			let slug = ''
			if (this.constructionPriceTypeSelected.length > 0) {
				slug = this.constructionPriceTypeSelected[0]
			}
			if (typeof this.form.tangible_assets !== 'undefined' && this.form.tangible_assets.length > 0) {
				this.form.tangible_assets.forEach((item, indexTangible) => {
					let price = 0
					let total = 0
					let average = 0
					item.construction_company.forEach((company_item) => {
						average = average + company_item.unit_price_m2
					})
					price = parseFloat(average / item.construction_company.length)
					this.total_average.push(price.toFixed(0))
					if (slug === 'dg-quyet-dinh') {
						price = item.total_desicion_average
					}
					this.remaining_building_price_choosing.push(price)
					total = Math.round(price * this.remaining_quality_choosing[indexTangible] / 100 * item.total_construction_base)
					total = total || 0
					this.remaining_building_price.push(total)
					this.total_remaining_building_price += total
				})
			}
		},
		onCancel () {
			return this.$router.push({name: 'certification_asset.index'})
		},
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		formatCurrent (value) {
			return Math.floor(value / 100000) * 100000
		},
		async saveContruction (data, contructionSelected) {
			await this.form.tangible_assets.forEach(item => {
				data.tangible_assets.forEach(itemData => {
					if (item.id === itemData.id) {
						item.construction_company = itemData.construction_company
					}
				})
			})
			this.contructionSelected = await contructionSelected
			this.average()
			this.render_tab_3 += 1
		},
		handleEditContruction () {
			this.openListContruction = true
			this.contruction_key += 1
		},
		changeContructionCompany (e, index) {
			if (e) {
				this.getTotal()
			}
		},
		changeDesicionAverage (event, index) {
			this.form.tangible_assets[index].total_desicion_average = event
			this.average()
		},
		changeP1 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.p1 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.p1 = 0
			}
			this.getTotal()
		},
		changeP2 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.p2 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.p2 = 0
			}
			this.getTotal()
		},
		changeP3 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.p3 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.p3 = 0
			}
			this.getTotal()
		},
		changeP4 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.d4 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.d4 = 0
			}
			this.getTotal()
		},
		changeP5 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.p5 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.p5 = 0
			}
			this.getTotal()
		},
		changeH1 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.h1 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.h1 = 0
			}
			this.getTotal()
		},
		changeH2 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.h2 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.h2 = 0
			}
			this.getTotal()
		},
		changeH3 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.h3 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.h3 = 0
			}
			this.getTotal()
		},
		changeH4 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.h4 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.h4 = 0
			}
			this.getTotal()
		},
		changeH5 (e, index) {
			if (e) {
				this.form.tangible_assets[index].comparison_tangible_factor.h5 = e
			} else {
				this.form.tangible_assets[index].comparison_tangible_factor.h5 = 0
			}
			this.getTotal()
		},
		getTotal () {
			this.setRemainQualityRate(this.constructionRemainQualitySelected)
			this.total_remaining_building_price = 0
			this.form.tangible_assets.forEach((item, indexTangible) => {
				let p1 = item.comparison_tangible_factor.p1
				let p2 = item.comparison_tangible_factor.p2
				let p3 = item.comparison_tangible_factor.p3
				let d4 = item.comparison_tangible_factor.d4
				let p5 = item.comparison_tangible_factor.p5
				let h1 = item.comparison_tangible_factor.h1
				let h2 = item.comparison_tangible_factor.h2
				let h3 = item.comparison_tangible_factor.h3
				let h4 = item.comparison_tangible_factor.h4
				let h5 = item.comparison_tangible_factor.h5
				if (p1 + p2 + p3 + d4 + p5 !== 0) {
					this.total[indexTangible] = Math.round(parseFloat((p1 * h1 + p2 * h2 + p3 * h3 + d4 * h4 + p5 * h5) / (p1 + p2 + p3 + d4 + p5)).toFixed(1))
				} else {
					this.total[indexTangible] = 0
				}
				this.remaining_quality_average[indexTangible] = Math.round((item.remaining_quality + +this.total[indexTangible]) / 2)
			})
			this.average()
		},

		getData () {
			if (this.form.comparison_tangible_factor && this.form.comparison_tangible_factor.length > 0) {
				this.comparison_tangible_factor.push(this.form.comparison_tangible_factor)
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
		async beforeSave () {
			let message = ''
			if (this.constructionRemainQualitySelected.length === 0) {
				message = 'Vui lòng chọn phương pháp xác định chất lượng còn lại'
			}
			if (this.constructionPriceTypeSelected.length === 0) {
				message = 'Vui lòng chọn loại đơn giá xây dựng'
			}
			return message
		},
		async handleSaveTab3 () {
			let message = await this.beforeSave()
			if (message !== '') {
				this.openMessage(message)
				return
			}
			this.isSubmit = true
			const xac_dinh_clcl = this.constructionRemainQuality.find(i => i.slug === this.constructionRemainQualitySelected[0])
			const xac_dinh_dgxd = this.constructionPriceType.find(i => i.slug === this.constructionPriceTypeSelected[0])
			const res = await CertificateAsset.postDataComparison(this.form.tangible_assets, this.idData, xac_dinh_clcl, xac_dinh_dgxd)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu bảng điều chỉnh CTXD thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$emit('updateAssetPrice', res.data)
			} else if (res.error) {
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
			this.isSubmit = false
		}
	}
}
</script>
<style scoped lang="scss">
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
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;

    &__img {
      padding: 8px 20px;
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
  align-items: center;
  background: #FFFFFF;
  border-radius: 5.88235px;
  padding: 10px;
  // margin: auto;
  // width: 36px;
  // height: 36px;

  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
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
.main-wrapper {
  margin-top:1rem;
  width: 100%;
  overflow-x: auto;
  box-sizing: border-box;
}

.responsive-table {
  display: inline-block;
  min-width: 100%;
  box-sizing: border-box;
}

.responsive-table > table {
  width: 100%;
  border-collapse: collapse;
}
.table_detail_contruction {
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead{
    th{
      white-space: unset !important;
      padding: 12px 6px;
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
      &:first-child{
        width: 15%;
      }
      // &:nth-child(2) {
      //     width: 15%;
      // }
      &:nth-child(2) {
        width: 5%;
        max-width: 60px;
      }
      &:nth-child(3) {
        width: 14%;
      }
      &:nth-child(4) {
        width: 14%;
      }
      &:nth-child(4) {
        width: 14%;
      }
      &:nth-child(5) {
        width: 14%;
      }
      &:nth-child(6) {
        width: 14%;
      }
      &:nth-child(7) {
        width: 14%;
      }
      // &:nth-child(8) {
      //   width: 14%;
      // }
      // &:last-child{
      // }
      box-sizing: border-box;
      padding: 10px 14px;
    }
  }
  }
  .table_contruction_pp1 {
    width: 100%;
    font-weight: 500;
    color: #000000;
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
        //   width: 5%;
        // }
        &:nth-child(3) {
          width: 5%;
          min-width: 150px;
        }
        &:nth-child(4) {
          width: 16%;

        }
        // &:nth-child(4) {
        //   width: 16%;

        // }
        // &:nth-child(5) {
        //   width: 16%;

        // }
        // &:nth-child(6) {
        //   width: 16%;

        // }
        // &:nth-child(7) {
        //   width: 16%;

        // }
        &:last-child{
          width: 15%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
  }
  .table_contruction_pp2 {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color:  #DEE6EE;;
        color: #3D4D65;
        border: 1px solid white;
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
        &:first-child{
          width: 15%;
        }
        // &:nth-child(2) {
        //   width: 15%;
        // }
        &:last-child{
          width: 10%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
  }
  .table_contruction_result {
    width: 100%;
    font-weight: 500;
    color: #000000;
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
      // tr{
      //    &:nth-child(2) {
      //     color: #3D4D65;
      //     background-color: rgba(222, 230, 238, 0.5);
      //   }
      // }
      td{
        border: 1px solid #CED4DA;
        // &:first-child{
        //   width: 5%;
        // }
        &:nth-child(3) {
          width: 5%;
          min-width: 150px;
        }
        &:nth-child(4) {
          width: 16%;

        }
        &:last-child{
          width: 15%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
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
    font-size: 1.125rem;
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
    background-color: rgba(0,207,232,.12);
    color: #00CFE8!important;
  }
    .content_detail_asset {
    margin-top: 1rem;
  }
  .contruction_block {
    margin-bottom: 1.5rem;
  }
  .color_tr {
    color: #3D4D65;
    background-color: rgba(222, 230, 238, 0.5);
  }
  ::-webkit-scrollbar {
    width: 5px
  }
  ::-webkit-scrollbar-thumb {
    border-radius: 10px;
    background: gray;
  }
  .input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 22px;
  input {
    width: 22px;
    height: 22px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #ffffff;
        &:after {
          display: block;
        }
      }
    }
    &:disabled {
      & ~ .check-mark {
        background-color: #DEE6EE;
      }
    }
  }
  .check-mark {
    position: unset;
    top: 0px;
    left: 0;
    align-items: center;
    cursor: pointer;
    width: 22px;
    height: 22px;
    font-size: 18px;
    font-weight: bold;
    color: #617F9E;
    background-color: #ffffff;
    border: 2px solid #617F9E;
    border-radius: 4px;
    &:after {
      content: "\2713";
      position: absolute;
      display: none;
      left: 50%;
      top: -1px;
      width: 5px;
      height: 10px;
      // border: solid #FFFFFF;
      // border-width: 0 3px 3px 0;
      -webkit-transform: rotate(0deg) translate(-125%, -25%);
      -ms-transform: rotate(0deg) translate(-125%, -25%);
      transform: rotate(0deg) translate(-125%, -25%);
    }
  }
}
.clcl_width {
  width: 15%;
}
.document_action {
	cursor: pointer;
	background: #FFFFFF;
}

</style>
