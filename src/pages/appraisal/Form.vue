<template>
  <div>
    <ValidationObserver tag="form" ref="data" @submit.prevent="validateBeforeSubmit">
      <div style="margin-bottom: 80px">
        <AppraisalInformation ref="appraisalInformation" :data="form" :propertyTypes="propertyTypes"
          :topographic="topographic" :provinces="provinces" :districts="districts" :wards="wards" :streets="streets"
          :distances="distances" :full_address="full_address" :appraisalPurposes="appraisalPurposes"
          :certificate_id="$route.name === 'appraisal.edit' ? form.id : null" :isHaveAppraiseId=isHaveAppraiseId
          @getAssetType="changeAssetType" @getAppraisers="getAppraisersData" @deleteAppraisers="deleteAppraisers" />
        <AppraisalPerson :data="form" :appraisersManager="appraisersManager" :appraisers="appraisers"
          :signAppraisers="signAppraisers" @handleChangeAppraiser="handleChangeAppraiser"
          v-if="form.appraises.length > 0" />
        <AppraiseInfo ref="appraiseInfo" :data="form" :appraisalFacility="appraisalFacility" :approach="approach"
          :methodsUsed="methodsUsed" :appraisalPrinciples="appraisalPrinciples" :basis="basis" :principle="principle"
          :approaches="approaches" :method="method" v-if="form.appraises.length > 0" />
        <LegalBasis :expertises="expertises" :constructs="constructs" :lands="lands" :local="local"
          :appraise_documents_valuation="form.legal_documents_on_valuation"
          :appraise_documents_land="form.legal_documents_on_land"
          :appraise_documents_construction="form.legal_documents_on_construction"
          :appraise_documents_local="form.legal_documents_on_local" @expertise="getExpertise" @land="getLand"
          @construct="getConstruct" @local="getLocal" v-if="form.appraises.length > 0" />
        <ConstructionUnit v-if="form.construction_company && form.construction_company.length > 0"
          :constructions="form.construction_company" />
        <PropertySelection ref="propertySelection" :landTypePurposes="type_purposes" :propertyTypes="propertyTypes"
          :data="propertyAssets" :assets="assets" :location="form.coordinates" :radius="distance" :formData="form"
          @action="getAsset" @updateAsset="updateAsset" @getRadius="getRadius" @savePropertyId="getDataID"
          v-if="form.appraises.length > 0" />
        <ComparatorSelection :comparison="comparison" :comparison_factor="form.comparison_factor_Tem"
          @saveComparison="getComparison" v-if="form.appraises.length > 0" />
        <!-- <OtherPicture
        v-if="form.appraises.length > 0"
        :files="form.other_documents"
        :delete_other_documents="form.delete_other_documents"
        @handleChange="handleChangeFiles"
      /> -->
        <!-- <div id="document_appraisal">
        <DocumentAppraisal
          ref="documentAppraisal"
          v-if="isSave"
          :idData="idData"
        />
      </div> -->
      </div>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-lg-flex d-block button-contain">
          <button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn-loading disabled': isSubmit }"
            type="submit" v-if="isSave === false && (add || edit)"> <img src="../../assets/icons/ic_save.svg"
              :class="{ 'd-none': isSubmit }" style="margin-right: 12px" alt="save"> Xuất chứng thư</button>
          <button @click.prevent="handleOpenModalCancel" class="btn btn-white text-nowrap"
            :class="{ 'disabled': isSubmit }">
            <img src="../../assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="save">
            Hủy
          </button>
        </div>
      </div>
    </ValidationObserver>
    <ModalCancel v-if="openModalCancel" @cancel="openModalCancel = false" @action="handleCancel" />
    <ModalNotification v-if="openNotification" v-bind:notification="message" @cancel="openNotification = false"
      @action="handleHome" />
  </div>
</template>

<script>
import AppraisalInformation from './components/AppraisalInformation'
import OtherProperty from './components/OtherProperty'
import PropertyLegal from './components/PropertyLegal'
import AppraisalPerson from './components/AppraisalPerson'
import AppraiseInfo from './components/AppraiseInfo'
import LegalBasis from './components/LegalBasis'
import ConstructionUnit from './components/ConstructionUnit'
import OtherPicture from './components/OtherPicture'
import PropertySelection from './components/PropertySelection'
import ComparatorSelection from '@/pages/appraisal/components/ComparatorSelection'
import DocumentAppraisal from '@/pages/appraisal/components/DocumentAppraisal'

import ModalCancel from '@/components/Modal/ModalCancel'
import ModalNotification from '@/components/Modal/ModalNotification'
import Certificate from '@/models/Certificate'
import { COMPARISON } from '@/enum/comparison-factor.enum'
import WareHouse from '@/models/WareHouse'
import AppraiseData from '../../models/CertificateAssetData'
import File from '@/models/File'

export default {
	name: 'Form',
	data () {
		return {
			isSave: false,
			idData: null,
			openNotification: false,
			isSubmit: false,
			openModalCancel: false,
			message: '',
			form: {
				status: '2',
				ticket_num: '',
				document_num: '',
				document_date: '',
				certificate_num: '',
				certificate_date: '',
				petitioner_name: 'Ông / Bà',
				petitioner_phone: '',
				petitioner_address: '',
				appraiser_id: '',
				appraiser_confirm_id: '',
				appraiser_manager_id: '',
				document_description: '- Giả thiết:\n- Giả thiết đặc biệt:',
				legal_documents_on_valuation: [],
				legal_documents_on_land: [],
				legal_documents_on_construction: [],
				legal_documents_on_local: [],
				construction_company: [],
				certificate_basis_property: [],
				certificate_principle: [],
				certificate_approach: [],
				certificate_method_used: [],
				created_by: '',
				asset_general: [],
				assets: [],
				appraises: [],
				comparison_factor: [],
				comparison_factor_Tem: [],
				appraisal_id_old: [],
				other_documents: [],
				delete_other_documents: []
			},
			housingTypes: [],
			buildingCategories: [],
			propertyTypes: [],
			topographic: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			juridicals: [],
			appraisersManager: [],
			appraisers: [],
			signAppraisers: [],
			appraisalPurposes: [],
			appraisalFacility: [],
			approach: [],
			methodsUsed: [],
			appraisalPrinciples: [],
			type_purposes: [],
			expertises: [],
			constructs: [],
			lands: [],
			local: [],
			constructions: [],
			provinceName: null,
			districtName: null,
			wardName: null,
			streetName: null,
			assetName: null,
			full_address: null,
			unit_price: {
				province: null,
				district: null,
				ward: null,
				street: null,
				distance: null
			},
			compare_assets: [],
			landType: [],
			data: {
				assets: []
			},
			radius: 1,
			distance: 1000,
			property: null,
			basis: [],
			principle: [],
			approaches: [],
			method: [],
			propertyAssets: [],
			assets: [],
			comparison: COMPARISON,
			dataTem: [],
			comparisonTemplate: [],
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			isHaveAppraiseId: [],
			appraisers_check_document: []
		}
	},
	components: {
		AppraisalInformation,
		OtherProperty,
		PropertyLegal,
		AppraisalPerson,
		AppraiseInfo,
		LegalBasis,
		ModalCancel,
		ModalNotification,
		ConstructionUnit,
		PropertySelection,
		ComparatorSelection,
		DocumentAppraisal,
		OtherPicture
	},
	created () {
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
		if ('id' in this.$route.query && this.$route.name === 'appraisal.edit') {
			this.form = Object.assign(this.form, {
				...this.$route.meta['detail']
			})
			this.idData = this.form.id
			if (!this.form.petitioner_name) this.form.petitioner_name = 'Ông / Bà'
			// this.$nextTick(() => {
			//   this.$refs.documentAppraisal.getDataAppraises(this.form.id)
			// })
			if (this.form && this.form.appraises && this.form.appraises.length > 0) {
				this.form.appraises.forEach(data => {
					this.isHaveAppraiseId.push(data.appraise_id)
				})
			}
			this.$nextTick(() => {
				this.$refs.appraisalInformation.getDataEdit()
				this.$refs.appraisalInformation.getAssetEdit(this.form.appraises)
				// this.getExpertiseUpdate(this.form.legal_documents_on_valuation)
				// this.getLandUpdate(this.form.legal_documents_on_land)
				// this.getConstructUpdate(this.form.legal_documents_on_construction)
				// this.getLocalUpdate(this.form.legal_documents_on_local)
			})
		} else {
		}
	},
	methods: {
		handleChangeAppraiser (event) {
			if (event) {
				this.form.appraiser_confirm_id = ''
				if (event === this.form.appraiser_manager_id) {
					this.signAppraisers = this.appraisers
				} else {
					const filterData = this.appraisers.filter(item => item.id !== event)
					this.signAppraisers = filterData
				}
			} else {
				this.form.appraiser_confirm_id = ''
				this.signAppraisers = this.appraisers
			}
		},
		handleChangeFiles (file) {
			this.form.other_documents = file
		},
		async getAppraisersData (data) {
			this.form.construction_company = []
			this.form.comparison_factor = []
			this.form.comparison_factor_Tem = []
			this.constructions = []
			this.form.appraises = []
			this.form.certificate_basis_property = []
			this.basis = []
			this.principle = []
			this.form.certificate_principle = []
			this.form.certificate_approach = []
			this.approaches = []
			this.form.certificate_method_used = []
			this.propertyAssets = []
			this.method = []
			this.assets = data
			this.appraisers_check_document = []
			this.juridicals = []
			this.expertises = []
			this.constructs = []
			this.lands = []
			this.local = []
			this.isSubmit = true
			for (const item of data) {
				if (this.$route.name === 'appraisal.edit') {
					if (item.appraise_id) {
						// get data construction and comparison
						await this.getDataConstruction(item.construction_company, item.id, item.appraise_id)
						await this.getComparisonData(item.comparison_factor_custom, item.id, item.appraise_id)
					} else {
						await this.getDataConstruction(item.construction_company, item.id, null)
						await this.getComparisonData(item.comparison_factor_custom, item.id, null)
					}
				} else if (this.$route.name === 'appraisal.create') {
					await this.getDataConstruction(item.construction_company, item.id, null)
					await this.getComparisonData(item.comparison_factor_custom, item.id, null)
				}

				if (item.appraise_id) {
					this.form.appraises.push({
						appraise_id: item.appraise_id,
						version: '1.0'
					})
					await this.getAppraiseById(item.id)
				} else {
					await this.getAppraiseById(item.id, item.version[item.version.length - 1].version, item)
					this.form.appraises.push({
						appraise_id: item.id,
						version: '1.0'
					})
				}

				// if ((typeof item.version[item.version.length - 1] !== 'undefined')) {
				//   if (item.appraise_id) {
				//     this.form.appraises.push({
				//       appraise_id: item.appraise_id,
				//       version: item.version[item.version.length - 1].version
				//     })
				//     await this.getAppraiseById(item.id, item.version[item.version.length - 1].version, item)
				//   } else {
				//     this.form.appraises.push({
				//       appraise_id: item.id,
				//       version: item.version[item.version.length - 1].version
				//     })
				//     await this.getAppraiseById(item.id, item.version[item.version.length - 1].version)
				//   }
				// } else {
				//   if (item.appraise_id) {
				//     this.form.appraises.push({appraise_id: item.appraise_id})
				//     await this.getAppraiseById(item.id)
				//   } else {
				//     this.form.appraises.push({appraise_id: item.id})
				//     await this.getAppraiseById(item.id, null, null)
				//   }
				// }
			}
			await this.getAppraiseLaws()
			if (this.$route.name === 'appraisal.edit') {
				await this.getExpertiseUpdate(this.form.legal_documents_on_valuation)
				await this.getLandUpdate(this.form.legal_documents_on_land)
				await this.getConstructUpdate(this.form.legal_documents_on_construction)
				await this.getLocalUpdate(this.form.legal_documents_on_local)
			}
			if (!this.form.document_description && data && data.length > 0) {
				this.form.document_description = data[0].document_description
			}
			this.isSubmit = await false
		},
		async deleteAppraisers (index, data) {
			this.form.construction_company = []
			this.form.comparison_factor = []
			this.form.comparison_factor_Tem = []
			this.constructions = []
			this.form.certificate_basis_property = []
			this.basis = []
			this.principle = []
			this.form.certificate_principle = []
			this.form.certificate_approach = []
			this.approaches = []
			this.form.certificate_method_used = []
			this.propertyAssets = []
			this.method = []
			this.appraisers_check_document = []
			this.form.appraises.splice(index, 1)
			for (const item of data) {
				await this.getAppraiseById(item.id, item.version[item.version.length - 1].version, item)
			}
		},
		async getAppraiseById (id, version, dataTSTD) {
			if (id && !dataTSTD) {
				const res = await AppraiseData.getAppraiseAssetID(id, version)
				if (res.data) {
					this.getBasic(res.data)
					this.getPrinciple(res.data)
					this.getApproach(res.data)
					this.getMethod(res.data)
					this.getDataDescription(res.data)
					this.getPropertyAssets(res.data)
					this.appraisers_check_document.push(res.data.province)
				}
			}
			if (dataTSTD) {
				this.getBasic(dataTSTD)
				this.getPrinciple(dataTSTD)
				this.getApproach(dataTSTD)
				this.getMethod(dataTSTD)
				this.getDataDescription(dataTSTD)
				this.getPropertyAssets(dataTSTD)
				this.appraisers_check_document.push(dataTSTD.province)
			}
		},
		async getDataConstruction (data_company, newID, oldID) {
			await this.form.construction_company.push({
				id: newID,
				oldID: oldID,
				construction_company: data_company
			})
		},
		// get data facilty-value detail
		getBasic (data) {
			if (this.form.certificate_basis_property.length > 0 && data) {
				const id = this.form.certificate_basis_property.find(item => data.appraise_basis_property.id === item.certificate_basis_property_id)
				if (id === undefined || id === null) {
					this.form.certificate_basis_property.push({
						certificate_basis_property_id: data.appraise_basis_property.id
					})
					this.basis.push(data.appraise_basis_property)
				}
			} else {
				if (data) {
					this.form.certificate_basis_property.push({
						certificate_basis_property_id: data.appraise_basis_property.id
					})
					this.basis.push(data.appraise_basis_property)
				}
			}
		},
		getPrinciple (data) {
			if (this.form.certificate_principle.length > 0) {
				const id = this.form.certificate_principle.find(item => data.appraise_principle.id === item.certificate_principle_id)
				if (id === undefined || id === null) {
					this.form.certificate_principle.push({
						certificate_principle_id: data.appraise_principle.id
					})
					this.principle.push(data.appraise_principle)
				}
			} else {
				this.form.certificate_principle.push({
					certificate_principle_id: data.appraise_principle.id
				})
				this.principle.push(data.appraise_principle)
			}
		},
		getApproach (data) {
			if (this.form.certificate_approach.length > 0) {
				const id = this.form.certificate_approach.find(item => data.appraise_approach.id === item.certificate_approach_id)
				if (id === undefined || id === null) {
					this.form.certificate_approach.push({
						certificate_approach_id: data.appraise_approach.id
					})
					this.approaches.push(data.appraise_approach)
				}
			} else {
				this.form.certificate_approach.push({
					certificate_approach_id: data.appraise_approach.id
				})
				this.approaches.push(data.appraise_approach)
			}
		},
		getMethod (data) {
			if (this.form.certificate_method_used.length > 0) {
				const id = this.form.certificate_method_used.find(item => data.appraise_method_used.id === item.certificate_method_used_id)
				if (id === undefined || id === null) {
					this.form.certificate_method_used.push({
						certificate_method_used_id: data.appraise_method_used.id
					})
					this.method.push(data.appraise_method_used)
				}
			} else {
				this.form.certificate_method_used.push({
					certificate_method_used_id: data.appraise_method_used.id
				})
				this.method.push(data.appraise_method_used)
			}
		},
		getDataDescription (data) {
			if (this.form.document_description === '' && this.form.appraises.length > 0) {
				this.form.document_description = data.document_description
			} else if (this.form.appraises.length === 0) {
				this.form.document_description = ''
			}
		},
		getRadius (data) {
			this.distance = data
		},
		// get map asset
		getPropertyAssets (data) {
			if (data && data.asset_general && data.asset_general.length > 0) {
				this.propertyAssets.push(data)
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
			}
		},
		// get data comparison factor
		async getComparisonData (dataCompare, newID, oldID) {
			let comparisionArray = []
			if (typeof dataCompare[newID] !== 'undefined') {
				await dataCompare[newID].forEach((factor) => {
					comparisionArray.push(factor.label)
				})
				await this.form.comparison_factor_Tem.push({
					id: newID,
					oldID: oldID,
					table: comparisionArray
				})
				await this.form.comparison_factor.push({
					id: newID,
					oldID: oldID,
					comparison_factor: dataCompare[newID]
				})
			}
		},
		getComparison (dataCompare, dataID, dataIdOld) {
			this.form.comparison_factor_Tem = []
			this.form.comparison_factor = []
			let arrayTem = []
			dataCompare.forEach((comparisonArray, index) => {
				arrayTem = []
				comparisonArray.forEach(comparison => {
					arrayTem.push({ label: comparison })
				})
				this.form.comparison_factor_Tem.push({
					id: dataID[index],
					oldID: dataIdOld ? dataIdOld[index] : null,
					table: comparisonArray
				})
				this.form.comparison_factor.push({
					id: dataID[index],
					oldID: dataIdOld ? dataIdOld[index] : null,
					comparison_factor: arrayTem
				})
			})
		},
		async validateBeforeSubmit (event) {
			const isValid = await this.$refs.data.validate()
			this.isSubmit = true
			if (isValid) {
				if ((this.form.status === 3 || this.form.status === 4) && this.form.appraises.length === 0) {
					this.$toast.open({
						message: 'Vui lòng chọn tài sản thẩm định',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else {
					await this.handleSubmit(event)
				}
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			if (this.$route.name === 'appraisal.edit') {
				this.updateDictionary(data, this.form.other_documents)
			} else {
				this.createDictionary(data, this.form.other_documents)
			}
		},
		async createDictionary (data, files) {
			const formData = new FormData()
			if (files.length > 0) {
				await files.forEach((element, i) => {
					formData.append('files[' + i + ']', element)
				})
			}
			try {
				const resp = await Certificate.create(data)
				if (resp) {
					if (resp.data) {
						if (files.length > 0) {
							const res = await File.uploadFileCertificate(formData, resp.data)
							if (res.data) {
								this.isSubmit = false
								this.idData = resp.data
								this.isSave = true
								this.message = 'Tạo mới Hồ sơ thẩm định ' + '<b>' + 'TSTD_' + resp.data + '</b>' + ' thành công.'
								this.openNotification = true
							} else if (res.error) {
								this.$toast.open({
									message: res.error.message,
									type: 'error',
									position: 'top-right',
									duration: 3000
								})
								this.isSubmit = false
							}
						} else {
							this.isSubmit = false
							this.idData = resp.data
							this.isSave = true
							this.message = 'Tạo mới Hồ sơ thẩm định ' + '<b>' + 'TSTD_' + resp.data + '</b>' + ' thành công.'
							this.openNotification = true
						}
					} else if (resp.error) {
						this.$toast.open({
							message: resp.error.message,
							type: 'error',
							position: 'top-right',
							duration: 3000
						})
						this.isSubmit = false
					}
				}
			} catch (err) {
				this.isSubmit = false
				if (err.response.data.data.errors.document_num) {
					this.$toast.open({
						message: err.response.data.data.errors.document_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				} else if (err.response.data.data.errors.certificate_num) {
					this.$toast.open({
						message: err.response.data.data.errors.certificate_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
		},
		async updateDictionary (data, files) {
			const formData = new FormData()
			if (files.length > 0) {
				await files.forEach((element, i) => {
					if (!element.certificate_id) {
						formData.append('files[' + i + ']', element)
					}
				})
			}
			try {
				const resp = new Certificate(data)
				await resp.save()
				if (resp.data) {
					if (files.length > 0) {
						const res = await File.uploadFileCertificate(formData, +resp.data)
						if (res.data) {
							this.isSubmit = false
							this.message = 'Chỉnh sửa hồ sơ thẩm định ' + '<b>' + 'HSTD_' + resp.data + '</b>' + ' thành công.'
							this.openNotification = true
						} else if (res.error) {
							this.$toast.open({
								message: res.error.message,
								type: 'error',
								position: 'top-right',
								duration: 3000
							})
							this.isSubmit = false
						}
					} else {
						this.isSubmit = false
						this.message = 'Chỉnh sửa hồ sơ thẩm định ' + '<b>' + 'HSTD_' + resp.data + '</b>' + ' thành công.'
						this.openNotification = true
					}
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
					this.isSubmit = false
				}
			} catch (err) {
				this.isSubmit = false
				if (err.response.data.data.errors.document_num) {
					this.$toast.open({
						message: err.response.data.data.errors.document_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				} else if (err.response.data.data.errors.certificate_num) {
					this.$toast.open({
						message: err.response.data.data.errors.certificate_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (typeof profile.data.user.id !== 'undefined') {
				this.form.created_by = profile.data.user.id
			}
		},
		async handleHome () {
			if (this.$route.name === 'appraisal.create') {
				const id = this.idData
				this.$router.push({ name: 'appraisal.detail', query: { id: id } }).catch(_ => { })
				// this.$router.push({name: 'appraisal.index'}).catch(_ => {
				// })
			} else if (this.$route.name === 'appraisal.edit') {
				this.$router.go(-1)
			}
		},
		handleOpenModalCancel () {
			this.openModalCancel = true
		},
		async handleCancel () {
			this.isSubmit = true
			if (this.$route.name === 'appraisal.create') {
				return this.$router.push({ name: 'appraisal.index' })
			} else if (this.$route.name === 'appraisal.edit') {
				this.$router.go(-1)
			}
		},
		async changeProvince (id) {
			this.form.district_id = ''
			this.form.ward_id = ''
			this.form.street_id = ''
			this.form.distance_id = ''
			this.districts = []
			this.wards = []
			this.streets = []
			this.distances = []
			if (this.form.province_id !== 0 && this.form.province_id !== '' && this.form.province_id !== undefined && this.form.province_id !== null) {
				await this.getDistrictsByProvinceId(id)
			}
			this.findProvince()
		},
		findProvince () {
			const province = this.provinces.find(province => province.id === this.form.province_id)
			if (province) {
				this.provinceName = province.name
				this.unit_price.province = province.name
			} else {
				this.provinceName = null
				this.unit_price.province = null
			}
			this.findDistrict()
			this.getFullAddress()
		},
		async changeDistrict (id) {
			this.wards = []
			this.streets = []
			this.distances = []
			this.form.ward_id = ''
			this.form.street_id = ''
			this.form.distance_id = ''
			if (this.form.district_id !== 0 && this.form.district_id !== '' && this.form.district_id !== undefined && this.form.district_id !== null) {
				await this.getWardsByDistrictId(id)
				await this.getStreetByDistrictId(id)
			}
			this.findDistrict()
		},
		findDistrict () {
			const district = this.districts.find(district => district.id === this.form.district_id)
			if (district) {
				this.districtName = district.name
				this.unit_price.district = district.name
			} else {
				this.districtName = null
				this.unit_price.district = null
			}
			if (this.districtName && (this.districtName.toLowerCase() === 'thành phố biên hòa' || this.districtName.toLowerCase() === 'thành phố long khánh')) {
				this.radius = 1
				this.distance = 1000
			} else {
				this.radius = 2
				this.distance = 2000
			}
			this.findWard()
			this.findStreet()
			this.getFullAddress()
		},
		changeWard () {
			this.findWard()
		},
		findWard () {
			const ward = this.wards.find(ward => ward.id === this.form.ward_id)
			if (ward) {
				this.wardName = ward.name
				this.unit_price.ward = ward.name
			} else {
				this.wardName = null
				this.unit_price.ward = null
			}
			this.getFullAddress()
		},
		async changeStreet (id) {
			this.distances = []
			this.form.distance_id = ''
			if (this.form.street_id !== 0 && this.form.street_id !== '' && this.form.street_id !== undefined && this.form.street_id !== null) {
				await this.getDistanceByStreetId(id)
			}
			this.findStreet()
		},
		findStreet () {
			const street = this.streets.find(street => street.id === this.form.street_id)
			if (street) {
				this.streetName = street.name
				this.unit_price.street = street.name
			} else {
				this.streetName = null
				this.unit_price.street = null
			}
			this.findDistance()
			this.getFullAddress()
		},
		changeDistance () {
			this.findDistance()
		},
		findDistance () {
			const distance = this.distances.find(distances => distances.id === this.form.distance_id)
			if (distance) {
				this.unit_price.distance = distance.name
			} else {
				this.unit_price.distance = null
			}
		},
		changeAssetType () {
			const assetType = this.propertyTypes.find(assetType => assetType.id === this.form.asset_type_id)
			if (assetType) {
				this.assetName = assetType.description
			} else {
				this.assetName = null
			}
			this.getInfo()
		},
		getFullAddress () {
			this.full_address = `${this.streetName ? this.streetName + ', ' : ''}` + `${this.wardName ? this.wardName + ', ' : ''}` + `${this.districtName ? this.districtName + ', ' : ''}` + `${this.provinceName ? this.provinceName : ''}`
			this.getInfo()
		},
		getInfo () {
			this.form.appraise_asset = `${this.assetName === 'ĐẤT TRỐNG' ? 'Quyền sử dụng đất tại' : this.assetName === 'ĐẤT CÓ NHÀ' ? 'Quyền sử dụng đất và CTXD tại' : ''}` + `${this.streetName ? ' đường ' : ''}` + `${this.full_address ? this.full_address : ''}`
		},
		async getDictionary () {
			try {
				const resp = await WareHouse.getDictionaries()
				this.topographic = [...resp.data.dia_hinh]
				this.propertyTypes = [...resp.data.loai_tai_san]
				this.landType = [...resp.data.loai_dat]
				this.landShapes = [...resp.data.hinh_dang_dat]
				this.socialSecurities = [...resp.data.an_ninh_moi_truong_song]
				this.businesses = [...resp.data.kinh_doanh]
				this.paymentMethods = [...resp.data.dieu_kien_thanh_toan]
				this.conditions = [...resp.data.dieu_kien_ha_tang]
				this.fengshuies = [...resp.data.phong_thuy]
				this.zones = [...resp.data.quy_hoach_hien_trang]
				this.type_purposes = [...resp.data.loai_dat_chi_tiet]
				this.materials = [...resp.data.giao_thong_chat_lieu]
				this.roughes = [...resp.data.giao_thong]
				this.points = [...resp.data.vi_tri_dat]
				this.housingTypes = [...resp.data.loai_nha]
				this.buildingCategories = [...resp.data.cap_nha]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getAppraiseLaws () {
			const map = new Map()
			const resp = await Certificate.getAppraiseLaws()
			let filterResponse = {}
			const checkLabelArray = ['dat_dai', 'dia_phuong', 'phap_ly', 'tham_dinh_gia', 'xay_dung']
			if (resp.data) {
				checkLabelArray.forEach(label => {
					let response_data_temp = [...resp.data[label]]
					let appraise_law = []
					// get element by name "Tất cả"
					const all_filter = response_data_temp.filter(data => data.provinces === 'Tất cả')
					appraise_law.push(...all_filter)
					// get element by name province
					if (this.appraisers_check_document) {
						this.appraisers_check_document.forEach(item => {
							if (!map.has(item.id)) {
								map.set(item.id, true)
								const province_filter = response_data_temp.filter(data => {
									if (data.provinces) { data.provinces = data.provinces.trim() }
									if (item.name) { item.name = item.name.trim() }
									return data.provinces === item.name
								})
								appraise_law.push(...province_filter)
							}
						})
						map.clear()
					}
					filterResponse[label] = appraise_law
				})
			}
			if (filterResponse.phap_ly) {
				this.juridicals = [...filterResponse.phap_ly]
			} else {
				this.juridicals = []
			}
			if (filterResponse.tham_dinh_gia) {
				this.expertises = [...filterResponse.tham_dinh_gia]
			} else {
				this.expertises = []
			}
			if (filterResponse.xay_dung) {
				this.constructs = [...filterResponse.xay_dung]
			} else {
				this.constructs = []
			}
			if (filterResponse.dat_dai) {
				this.lands = [...filterResponse.dat_dai]
			} else {
				this.lands = []
			}
			if (filterResponse.dia_phuong) {
				this.local = [...filterResponse.dia_phuong]
			} else {
				this.local = []
			}
			await this.getData()
		},
		// create document-law
		getData () {
			if (this.$route.name === 'appraisal.create') {
				this.form.legal_documents_on_valuation = []
				this.form.legal_documents_on_land = []
				this.form.legal_documents_on_construction = []
				this.form.legal_documents_on_local = []
				this.expertises.forEach(expertise => {
					if (expertise.is_defaults) {
						this.form.legal_documents_on_valuation.push({ certificate_law_id: expertise.id })
					}
				})
				this.lands.forEach(land => {
					if (land.is_defaults) {
						this.form.legal_documents_on_land.push({ certificate_law_id: land.id })
					}
				})
				this.constructs.forEach(construct => {
					if (construct.is_defaults) {
						this.form.legal_documents_on_construction.push({ certificate_law_id: construct.id })
					}
				})
				this.local.forEach(local => {
					if (local.is_defaults) {
						this.form.legal_documents_on_local.push({ certificate_law_id: local.id })
					}
				})
			}
		},
		// get data document-law
		getExpertise (event) {
			this.form.legal_documents_on_valuation = []
			event.forEach(e => {
				this.form.legal_documents_on_valuation.push({
					certificate_law_id: e
				}
				)
			})
		},
		getLand (event) {
			this.form.legal_documents_on_land = []
			event.forEach(e => {
				this.form.legal_documents_on_land.push({
					certificate_law_id: e
				}
				)
			})
		},
		getConstruct (event) {
			this.form.legal_documents_on_construction = []
			event.forEach(e => {
				this.form.legal_documents_on_construction.push({
					certificate_law_id: e
				}
				)
			})
		},
		getLocal (event) {
			this.form.legal_documents_on_local = []
			event.forEach(e => {
				this.form.legal_documents_on_local.push({
					certificate_law_id: e
				}
				)
			})
		},
		// update document-law
		getExpertiseUpdate (data) {
			const expertise = data
			this.form.legal_documents_on_valuation = []
			if (expertise && expertise.length > 0) {
				expertise.forEach(e => {
					let filter = []
					if (e.id) {
						filter = this.expertises.filter(item => item.id === e.id)
					} else filter = this.expertises.filter(item => item.id === e.certificate_law_id)

					if (filter && filter.length > 0 && e.id) {
						this.form.legal_documents_on_valuation.push({ certificate_law_id: e.id })
					} else if (filter && filter.length > 0 && e.certificate_law_id) {
						this.form.legal_documents_on_valuation.push({ certificate_law_id: e.certificate_law_id })
					}
				})
			} else {
				this.expertises.forEach(expertise => {
					if (expertise.is_defaults) {
						this.form.legal_documents_on_valuation.push({ certificate_law_id: expertise.id })
					}
				})
			}
		},
		getLandUpdate (data) {
			const land = data
			this.form.legal_documents_on_land = []
			if (land && land.length > 0) {
				land.forEach(e => {
					let filter = []
					if (e.id) {
						filter = this.lands.filter(item => item.id === e.id)
					} else filter = this.lands.filter(item => item.id === e.certificate_law_id)

					if (filter && filter.length > 0 && e.id) {
						this.form.legal_documents_on_land.push({ certificate_law_id: e.id })
					} else if (filter && filter.length > 0 && e.certificate_law_id) {
						this.form.legal_documents_on_land.push({ certificate_law_id: e.certificate_law_id })
					}
				})
			} else {
				this.lands.forEach(land => {
					if (land.is_defaults) {
						this.form.legal_documents_on_land.push({ certificate_law_id: land.id })
					}
				})
			}
		},
		getConstructUpdate (data) {
			const construct = data
			this.form.legal_documents_on_construction = []
			if (construct && construct.length > 0) {
				construct.forEach(e => {
					let filter = []
					if (e.id) {
						filter = this.constructs.filter(item => item.id === e.id)
					} else filter = this.constructs.filter(item => item.id === e.certificate_law_id)

					if (filter && filter.length > 0 && e.id) {
						this.form.legal_documents_on_construction.push({ certificate_law_id: e.id })
					} else if (filter && filter.length > 0 && e.certificate_law_id) {
						this.form.legal_documents_on_construction.push({ certificate_law_id: e.certificate_law_id })
					}
				})
			} else {
				this.constructs.forEach(construct => {
					if (construct.is_defaults) {
						this.form.legal_documents_on_construction.push({ certificate_law_id: construct.id })
					}
				})
			}
		},
		getLocalUpdate (data) {
			const local = data
			this.form.legal_documents_on_local = []
			if (local && local.length > 0) {
				local.forEach(e => {
					let filter = []
					if (e.id) {
						filter = this.local.filter(item => item.id === e.id)
					} else filter = this.local.filter(item => item.id === e.certificate_law_id)

					if (filter && filter.length > 0 && e.id) {
						this.form.legal_documents_on_local.push({ certificate_law_id: e.id })
					} else if (filter && filter.length > 0 && e.certificate_law_id) {
						this.form.legal_documents_on_local.push({ certificate_law_id: e.certificate_law_id })
					}
				})
			} else {
				this.local.forEach(local => {
					if (local.is_defaults) {
						this.form.legal_documents_on_local.push({ certificate_law_id: local.id })
					}
				})
			}
		},
		// get data group appraise
		async getAppraisersManager () {
			const resp = await Certificate.getAppraisersManager()
			this.appraisersManager = await [...resp.data]
			this.form.appraiser_manager_id = await this.appraisersManager[0].id
		},
		async getAppraisers () {
			const resp = await Certificate.getAppraisers()
			let dataAppraise = await [...resp.data]

			if (this.form.appraiser_manager_id) {
				this.appraisers = await dataAppraise.filter(item => item.id !== this.form.appraiser_manager_id)
			}
			if (this.form.appraiser_id) {
				const filterData = await dataAppraise.filter(item => item.id !== this.form.appraiser_id && item.id !== this.form.appraiser_manager_id)
				this.signAppraisers = await filterData
			} else {
				this.signAppraisers = await this.appraisers
			}
		},
		async getAppraiseOthers () {
			const resp = await Certificate.getAppraiseOthers()
			this.appraisalPurposes = await [...resp.data.muc_dich_tham_dinh_gia]
			this.appraisalFacility = await [...resp.data.co_so_tham_dinh]
			this.appraisalPrinciples = await [...resp.data.nguyen_tac_tham_dinh]
			this.approach = await [...resp.data.cach_tiep_can_chi_phi]
			this.methodsUsed = await [...resp.data.phuong_phap_tham_dinh_su_dung]
			if (this.$route.name === 'appraisal.create') {
				await this.findAppraisalPurposes()
				await this.findAppraisalFacility()
				await this.findAppraisalPrinciples()
			}
		},
		findAppraisalPurposes () {
			if (this.appraisalPurposes.find(appraisalPurpose => appraisalPurpose.is_defaults === true)) { this.form.appraise_purpose_id = this.appraisalPurposes.find(appraisalPurpose => appraisalPurpose.is_defaults === true).id }
		},
		findAppraisalFacility () {
			if (this.appraisalFacility.find(appraisalFacility => appraisalFacility.is_defaults === true)) { this.form.appraise_basis_property_id = this.appraisalFacility.find(appraisalFacility => appraisalFacility.is_defaults === true).id }
		},
		findAppraisalPrinciples () {
			if (this.appraisalPrinciples.find(appraisalPrinciple => appraisalPrinciple.is_defaults === true)) { this.form.appraise_principle_id = this.appraisalPrinciples.find(appraisalPrinciple => appraisalPrinciple.is_defaults === true).id }
		},
		// action of selection property map detail HSTD
		getCompareAssets (data) {
			this.compare_assets = data
		},
		async getAsset () {
			let unrecognized = []
			if ((typeof this.form.properties !== 'undefined') && (this.form.properties.length > 0)) {
				this.form.properties[0].property_detail.forEach(property => {
					unrecognized.push({
						land_type_purpose: property.land_type_purpose_id,
						area: property.total_area
					})
				})
			}
			let body = {
				province_id: this.form.province_id,
				district_id: this.form.district_id,
				ward_id: this.form.ward_id,
				street_id: this.form.street_id,
				location: this.form.coordinates,
				front_side: this.form.properties.length > 0 ? this.form.properties[0].front_side : '',
				main_road_length: this.form.properties.length > 0 && this.form.properties[0].property_detail.length > 0 ? this.form.properties[0].property_detail[this.form.properties[0].property_detail.length - 1].main_road_length : '',
				distance: this.radius,
				unrecognized: unrecognized
			}
			if (this.form.province_id && this.form.province_id !== '' && this.form.district_id && this.form.district_id !== '' && this.form.ward_id && this.form.ward_id !== '' && this.form.street_id && this.form.street_id !== '' && this.form.coordinates && this.form.coordinates !== '' && this.form.properties.length > 0) {
				const res = await AppraiseData.getAsset(body)
				this.data = res.data
				this.form.assets = this.data.assets
				await this.getProperties()
			} else {
				this.data = {
					assets: []
				}
				this.form.assets = []
				this.$toast.open({
					message: 'Vui lòng kiểm tra lại dữ liệu đầu vào!',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async getProperties () {
			this.form.asset_general = []
			for (const asset of this.data.assets) {
				await this.getAssetGeneralDetail(asset)
			}
		},
		async getAssetGeneralDetail (asset) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(asset.id)
			this.property = resp.data
			await this.getPropertyId(asset.id, asset.version)
			this.isSubmit = false
		},
		getPropertyId (id, version) {
			this.form.asset_general.push({
				asset_general_id: id,
				property_id: this.property.properties[0].id,
				version: version
			})
		},
		getDataID (id, index) {
			this.form.asset_general[index].property_id = id
		},
		async updateAsset (data) {
			this.data.assets = data
			this.form.asset = data
			await this.getProperties()
		}
	},
	async beforeMount () {
		this.getDictionary()
		await this.getAppraisersManager()
		await this.getAppraisers()
		this.getAppraiseOthers()
		this.getProfiles()
		// this.getComparisonData()
	}
}
</script>
<style lang="css">
.vue-tabs .nav-tabs>li.active>a,
.vue-tabs .nav-tabs>li.active>a:hover,
.vue-tabs .nav-tabs>li.active>a:focus {
  background-color: #FAA831;
  font-weight: 900;
}

.vue-tabs .nav-tabs>li>a {
  margin-right: 10px;
}
</style>
