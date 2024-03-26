<template>
	<div
		class="container-fluid"
		:style="isMobile ? { margin: '0', padding: '0' } : {}"
	>
		<div v-if="!isMobile" class="certification-asset">
			<form-wizard
				ref="wizard"
				color="#99D161"
				:title="
					`UTG${tempPriceEstimates.id ? `_${tempPriceEstimates.id}` : ''}`
				"
				:subtitle="status_text"
				layout="vertical"
				finish-button-text="Hoàn Thành"
				back-button-text="Thoát"
				next-button-text="Lưu"
				:startIndex="miscVariable.step_active || 0"
				@on-change="handleChange"
				class="vertical-steps steps-transparent"
				:class="{ step7: isStep3Active }"
			>
				<div>
					<button
						class="btn btn-orange btn-print btn-extra"
						@click="handlePrint"
					>
						<!-- <font-awesome-icon icon="print" /> -->
						<img src="@/assets/icons/ic_printer_white.svg" alt="print" />
					</button>
					<!-- <button
						class="btn btn-orange btn-history btn-extra"
						@click="showHistoryDrawer"
					>
						<img src="@/assets/icons/ic_log_history.svg" alt="history" />
					</button>
					<button
						class="btn btn-orange btn-additional btn-extra"
						@click="showAdditionalDrawer"
					>
						<img src="@/assets/icons/ic_category.svg" alt="additional" />
					</button> -->
					<a-drawer
						width="400"
						title="Lịch sử hoạt động"
						placement="right"
						:visible="visibleHistoryDrawer"
						@close="onHistoryDrawerClose"
					>
						<a-timeline>
							<a-timeline-item
								v-for="(item, index) in historyList"
								:key="index"
								color="green"
							>
								<template #dot>
									<img
										class="dot-image"
										:src="
											item.causer && item.causer.image
												? item.causer.image
												: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
										"
										style="width: 2em"
									/>
								</template>
								<p>
									<strong>{{
										item.causer && item.causer.name
											? item.causer.name
											: "Không xác	định"
									}}</strong>
								</p>
								<p>{{ item.description }}</p>
								<p>{{ formatDateTime(item.updated_at) }}</p>
							</a-timeline-item>
						</a-timeline>
					</a-drawer>
					<a-drawer
						width="600"
						placement="right"
						:visible="visibleAdditionalDrawer"
						:closable="false"
						@close="onAdditionalDrawerClose"
					>
						<div class="card">
							<div class="card-title">
								<div class="d-flex justify-content-between align-items-center">
									<h3 class="title">Thông tin tham khảo</h3>
									<img
										class="img-dropdown"
										:class="!showDetailPlanning ? 'img-dropdown__hide' : ''"
										src="@/assets/images/icon-btn-down.svg"
										alt="dropdown"
										@click="showDetailPlanning = !showDetailPlanning"
									/>
								</div>
							</div>
							<div
								class="card-body card-sub_header_title"
								v-show="showDetailPlanning"
							>
								<div class="container-fluid row">
									<div class="col-12 mb-2">
										<InputTextarea
											v-model="real_estate.planning_info"
											:disableInput="!isEditStatus"
											label="Thông tin quy hoạch"
											class="form-group-container"
											:autosize="true"
										/>
									</div>
									<div class="col-12 mb-2">
										<InputTextarea
											v-model="real_estate.planning_source"
											:disableInput="!isEditStatus"
											label="Nguồn thông tin"
											class="form-group-container"
											:autosize="true"
										/>
									</div>
									<div class="col-12 mb-2">
										<InputText
											v-model="real_estate.contact_person"
											:disabledInput="!isEditStatus"
											label="Người hướng dẫn khảo sát"
											class="form-group-container"
										/>
									</div>
									<div class="col-12 mb-2">
										<InputText
											v-model="real_estate.contact_phone"
											:disabledInput="!isEditStatus"
											label="Số điện thoại"
											class="form-group-container"
										/>
									</div>
								</div>
							</div>
						</div>
						<div
							class="btn-drawer-footer btn-footer d-md-flex d-block justify-content-end align-items-center"
						>
							<div class="d-md-flex d-block">
								<button
									v-if="isEditStatus"
									:class="{ 'btn_loading disabled': isSubmit }"
									class="btn btn-white btn-orange text-nowrap"
									@click.prevent="handleSaveAdditional"
								>
									<img
										src="@/assets/icons/ic_save.svg"
										style="margin-right: 12px"
										alt="save"
									/>Lưu
								</button>
							</div>
						</div>
					</a-drawer>
				</div>
				<div class="wizard-custom-info" v-if="tempPriceEstimates.createdBy">
					<div class="col-13">
						<div class="row d-flex">
							<p class="mb-1">Version :</p>
							<p class="mb-1">{{ tempPriceEstimates.max_version }}</p>
						</div>
						<div class="row d-flex" v-if="tempPriceEstimates.price_estimate_id">
							<p class="mb-1">Mã TSTĐ :</p>
							<a
								class="mb-1"
								:href="
									`/certification_asset/detail?id=${tempPriceEstimates.price_estimate_id}`
								"
								target="_blank"
								>{{ tempPriceEstimates.price_estimate_id }}</a
							>
						</div>
						<div class="">
							<p class="mb-1">Người được chỉnh sửa :</p>
							<div>
								<p class="mb-1">- {{ tempPriceEstimates.createdBy.name }}</p>
							</div>
						</div>
					</div>
				</div>

				<tab-content title="Thông tin chung" icon="">
					<ValidationObserver tag="div" ref="step_1">
						<Step1 :isEdit="isEdit" :key="miscInfo.key_step_1" />

						<div
							class="btn-footer d-md-flex d-block justify-content-end align-items-center"
						>
							<div class="d-lg-flex d-block button-contain">
								<button @click="onCancel" class="btn btn-white text-nowrap">
									<img
										src="@/assets/icons/ic_cancel.svg"
										style="margin-right: 12px"
										alt="save"
									/>Thoát
								</button>

								<button
									v-if="isEditStatus"
									class="btn btn-white"
									@click.prevent="handleEdit(0)"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_edit.svg"
										style="margin-right: 12px"
										alt="save"
									/>Chỉnh sửa
								</button>
							</div>
						</div>
					</ValidationObserver>
				</tab-content>

				<tab-content title="Tài sản so sánh" icon="">
					<ValidationObserver tag="div" ref="step_2">
						<Step2 />
						<div
							class="btn-footer d-md-flex d-block justify-content-end align-items-center"
						>
							<div class="d-lg-flex d-block button-contain">
								<button @click="onCancel" class="btn btn-white text-nowrap">
									<img
										src="@/assets/icons/ic_cancel.svg"
										style="margin-right: 12px"
										alt="save"
									/>Thoát
								</button>
								<button
									v-if="isEditStatus"
									class="btn btn-white"
									@click.prevent="handleEdit(1)"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_edit.svg"
										style="margin-right: 12px"
										alt="save"
									/>Chỉnh sửa
								</button>
								<!-- <button
									v-if="isEditStatus && isCancelEnable"
									@click.prevent="handleCancelProperty()"
									class="btn btn-white text-nowrap"
								>
									<img
										src="@/assets/icons/ic_destroy.svg"
										style="margin-right: 12px"
										alt="cancel"
									/>Hủy tài sản
								</button> -->
							</div>
						</div>
					</ValidationObserver>
				</tab-content>

				<tab-content title="Giá trị tài sản" icon=""
					><ValidationObserver tag="div" ref="step_3">
						<!-- <Step6 /> -->
						<Step3 :isEdit="isEdit" :key="miscInfo.key_step_3" />
						<div
							class="btn-footer d-md-flex d-block justify-content-end align-items-center"
						>
							<div class="d-lg-flex d-block button-contain">
								<button
									@click.prevent="handleChangeBack"
									class="btn btn-white text-nowrap"
								>
									<img
										src="@/assets/icons/ic_cancel.svg"
										style="margin-right: 12px"
										alt="save"
									/>Trở lại
								</button>
								<button
									v-if="isEditStatus"
									class="btn btn-white btn-orange text-nowrap"
									:class="{ 'btn_loading disabled': isSubmit }"
									@click.prevent="validateSubmitStep3"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_save.svg"
										style="margin-right: 12px"
										alt="save"
									/>Lưu
								</button>
								<button
									v-if="isEditStatus"
									class="btn btn-white  text-nowrap"
									style="background-color: #007ec6;color: white;"
									:class="{ 'btn_loading disabled': isSubmit }"
									@click.prevent="moveToAppraise"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_done.svg"
										style="margin-right: 12px"
										alt="done"
									/>Chuyển sang TSTĐ
								</button>
								<!-- <button
								v-if="isEdit && isCancelEnable"
								@click.prevent="handleCancelProperty()"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_destroy.svg"
									style="margin-right: 12px"
									alt="cancel"
								/>Hủy tài sản
							</button> -->
							</div>
						</div>
					</ValidationObserver>
				</tab-content>
			</form-wizard>
		</div>
		<div v-else class="certification-asset" style="margin-bottom: 190px;">
			<div>
				<button class="btn btn-orange btn-print btn-extra" @click="handlePrint">
					<!-- <font-awesome-icon icon="print" /> -->
					<img src="@/assets/icons/ic_printer_white.svg" alt="print" />
				</button>
				<button
					class="btn btn-orange btn-history btn-extra"
					@click="showHistoryDrawer"
				>
					<img src="@/assets/icons/ic_log_history.svg" alt="history" />
				</button>
				<button
					class="btn btn-orange btn-additional btn-extra"
					@click="showAdditionalDrawer"
				>
					<img src="@/assets/icons/ic_category.svg" alt="additional" />
				</button>
				<a-drawer
					width="100%"
					title="Lịch sử hoạt động"
					placement="right"
					:visible="visibleHistoryDrawer"
					@close="onHistoryDrawerClose"
					closeIcon="true"
				>
					<a-timeline>
						<a-timeline-item
							v-for="(item, index) in historyList"
							:key="index"
							color="green"
						>
							<template #dot>
								<img
									class="dot-image"
									:src="
										item.causer && item.causer.image
											? item.causer.image
											: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
									"
									style="width: 2em"
								/>
							</template>
							<p>
								<strong>{{
									item.causer && item.causer.name
										? item.causer.name
										: "Không xác	định"
								}}</strong>
							</p>
							<p>{{ item.description }}</p>
							<p>{{ formatDateTime(item.updated_at) }}</p>
						</a-timeline-item>
					</a-timeline>
				</a-drawer>
				<a-drawer
					width="100%"
					placement="right"
					:visible="visibleAdditionalDrawer"
					@close="onAdditionalDrawerClose"
					closeIcon="true"
				>
					<div class="card" style="    margin-top: 15px;">
						<div class="card-title">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="title">Thông tin tham khảo</h3>
								<img
									class="img-dropdown"
									:class="!showDetailPlanning ? 'img-dropdown__hide' : ''"
									src="@/assets/images/icon-btn-down.svg"
									alt="dropdown"
									@click="showDetailPlanning = !showDetailPlanning"
								/>
							</div>
						</div>
						<div
							class="card-body card-sub_header_title"
							v-show="showDetailPlanning"
						>
							<div class="container-fluid row">
								<div class="col-12 mb-2">
									<InputTextarea
										v-model="real_estate.planning_info"
										:disableInput="!isEditStatus"
										label="Thông tin quy hoạch"
										class="form-group-container"
										:autosize="true"
									/>
								</div>
								<div class="col-12 mb-2">
									<InputTextarea
										v-model="real_estate.planning_source"
										:disableInput="!isEditStatus"
										label="Nguồn thông tin"
										class="form-group-container"
										:autosize="true"
									/>
								</div>
								<div class="col-12 mb-2">
									<InputText
										v-model="real_estate.contact_person"
										:disabledInput="!isEditStatus"
										label="Người hướng dẫn khảo sát"
										class="form-group-container"
									/>
								</div>
								<div class="col-12 mb-2">
									<InputText
										v-model="real_estate.contact_phone"
										:disabledInput="!isEditStatus"
										label="Số điện thoại"
										class="form-group-container"
									/>
								</div>
							</div>
						</div>
					</div>
					<div
						class="btn-drawer-footer btn-footer d-md-flex d-block justify-content-end align-items-center"
					>
						<div class="d-md-flex d-block">
							<button
								v-if="isEditStatus"
								:class="{ 'btn_loading disabled': isSubmit }"
								class="btn btn-white btn-orange text-nowrap"
								@click.prevent="handleSaveAdditional"
							>
								<img
									src="@/assets/icons/ic_save.svg"
									style="margin-right: 12px"
									alt="save"
								/>Lưu
							</button>
						</div>
					</div>
				</a-drawer>
			</div>
			<!-- <div class="wizard-custom-info" v-if="createdBy">
			<div class="col-13">
				<div class="row d-flex">
					<p class="mb-1">Version :</p>
					<p class="mb-1">{{max_version}}</p>
				</div>
				<div class="row d-flex">
					<p class="mb-1">Mã HSTĐ :</p>
					<a class="mb-1" :href="`/certification_brief/detail?id=${form.certificate.id}`" v-if="form.certificate" target='_blank'>{{form.certificate ? form.certificate.id : ''}}</a>
					<p class="mb-1" v-else>{{form.certificate ? form.certificate.id : ''}}</p>
				</div>
				<div class="">
					<p class="mb-1">Người được chỉnh sửa :</p>
					<div>
						<p class="mb-1"> - {{createdBy.name}}</p>
					</div>
				</div>
			</div>
		</div> -->
		</div>
		<ModalPrintEstimateAssets
			v-if="openPrint"
			@cancel="openPrint = false"
			:data="priceEstimates"
		/>
		<ModalNotificationAppraisal
			v-if="openCancelAppraisal"
			@cancel="openCancelAppraisal = false"
			v-bind:notification="message"
			@action="handleActionCancelAppraise"
		/>
	</div>
</template>

<script>
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import "vue-form-wizard/dist/vue-form-wizard.min.css";
import { BCard, BRow, BCol, BFormGroup, BFormInput } from "bootstrap-vue";
import Step1 from "./componentDetail/Step1";
import Step2 from "./componentDetail/Step2";
import Step3 from "./component/Step3";
import { COMPARISON } from "@/enum/comparison-factor.enum";
import { Timeline, Drawer } from "ant-design-vue";
import PriceEstimateModel from "@/models/PriceEstimates";
import CertificateAsset from "@/models/CertificateAsset";
import moment from "moment";
import ModalPrintEstimateAssets from "@/components/Modal/ModalPrintEstimateAssetNew";
import ModalNotificationAppraisal from "@/components/Modal/ModalNotificationAppraisal";
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

export default {
	name: "Index",
	components: {
		"b-tooltip": BTooltip,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown,
		BCard,
		FormWizard,
		TabContent,
		WizardStep,
		BRow,
		BCol,
		BFormGroup,
		BFormInput,
		Timeline,
		InputText,
		InputTextarea,
		Drawer,
		Step1,
		Step2,
		Step3,
		ModalPrintEstimateAssets,
		ModalNotificationAppraisal
	},
	data() {
		return {
			idData: null,
			isSave: false,
			openNotification: false,
			openModalCancel: false,
			isCancelEnable: true,
			openCancelAppraisal: false,
			showDetailPlanning: true,
			message: "",
			visibleHistoryDrawer: false,
			visibleAdditionalDrawer: false,
			businesses: [],
			conditions: [],
			socialSecurities: [],
			fengshuies: [],
			zones: [],
			landShapes: [],
			points: [],
			key_step_1: 100000,
			key_step_2: 200,
			key_step_3: 3000,
			key_step_4: 10000,
			key_step_5: 40000,
			key_step_6: 50000,
			key_step_7: 60000,
			key_render_formwizard: 70000,
			isHaveContruction: false,
			step_active: null,
			form: {
				step_1: {
					economic_infomation: {
						business_id: "",
						condition_id: "",
						feng_shui_id: "",
						zoning_id: "",
						social_security_id: ""
					},
					general_infomation: {
						asset_type_id: "",
						province_id: "",
						district_id: "",
						ward_id: "",
						street_id: "",
						distance_id: "",
						appraise_asset: "",
						coordinates: "",
						topographic_id: ""
					},
					traffic_infomation: {
						front_side: "",
						individual_road: "",
						main_road_length: "",
						material: "",
						material_id: "",
						two_sides_land: "",
						property_turning_time: [
							{
								is_alley_with_connection: "",
								main_road_distance: "",
								main_road_length: "",
								material_id: "",
								turning: "Hẻm số 1"
							}
						],
						description: ""
					},
					geographical_location:
						"+ Hướng Đông:\n+ Hướng Tây:\n+ Hướng Nam:\n+ Hướng Bắc:",
					picture_infomation: []
				},
				step_2: {
					land_details: {
						front_side_width: "",
						insight_width: "",
						land_shape_id: "",
						coordinates: "",
						appraise_land_sum_area: 0,
						topographic: { topographic_id: "" }
					},
					total_area: [
						{
							land_type_purpose: {},
							land_type_purpose_id: "",
							total_area: "",
							is_transfer_facility: null
						}
					],
					planning_area: [
						// {
						//   land_type_purpose_id: '',
						//   planning_area: '',
						//   type_zoning: '',
						// }
					],
					thamkhao_area: [],
					UBND_price: [],
					real_estate: {
						planning_info: "",
						planning_source: "",
						contact_person: "",
						contact_phone: ""
					}
				},
				step_3: {
					construction: {
						building_type_id: "",
						gpxd: true,
						building_category_id: "",
						floor: "",
						remaining_quality: "",
						total_construction_base: "",
						total_construction_area: "",
						start_using_year: "",
						duration: "",
						description: "",
						other_building: "",
						rate_id: "",
						structure_id: "",
						crane_id: "",
						aperture_id: "",
						factory_type_id: "",
						created_at: new Date(),
						contruction_description:
							"+ Móng cột:\n+ Dầm, sàn BTCT chịu lực: \n+ Tường xây: \n+ Mái BTCT: \n+ Nền lát: \n+ Cửa đi, cửa sổ: \n+ Khu vệ sinh: \n+ Khu bếp: \n+ Cầu thang: \n+ Hiện trạng: \n"
					}
				},
				step_4: {
					law: []
				},
				step_5: {
					appraisal_methods: {
						thong_nhat_muc_gia_chi_dan: {
							slug_value: "",
							value: null
						},
						tinh_gia_dat_hon_hop_con_lai: {
							slug_value: "",
							value: null
						},
						tinh_gia_dat_vi_pham_quy_hoach: {
							slug_value: "",
							value: null
						}
					},
					value_base_and_approach: {
						document_description:
							"-	Về pháp lý: Khách hàng cam đoan chịu trách nhiệm về tính pháp lý các giấy tờ pháp lý do khách hàng cung cấp bằng bản photocopy và thông tin tài sản thẩm định giá.\n -	Số liệu về diện tích đất và công trình xây dựng: VietTin Valuation căn cứ vào các giấy tờ pháp lý khách hàng cung cấp và theo hiện trạng thực tế. Trong trường hợp tài sản thẩm định có thay đổi về diện tích đất khác với mô tả tại chứng thư định giá, VietTin Valuation đề nghị đánh giá lại giá trị tài sản.",
						information_overview: "",
						appraise_approach_id: "",
						appraise_basis_property_id: "",
						appraise_principle_id: "",
						appraise_method_used_id: "",
						information_overview: ""
					}
				},
				step_6: {
					comparison_factor: ["phap_ly"],
					assets_general: [],
					map_img: ""
				},
				step_7: {
					appraise_adapter: [],
					asset_price: [],
					asset_unit_area: [],
					asset_unit_price: [],
					comparison_factor: [],
					comparison_tangible_factor: [],
					construction_company: [],
					other_assets: []
				},
				construction_company_ids: [],
				// coordinates: '',
				appraise_asset: "",
				properties: [],
				tangible_assets: [],
				other_assets: [],
				appraise_law: [
					// {
					//   land_details: [
					//     {
					//       doc_no: '',
					//       land_no: ''
					//     }
					//   ]
					// }
				],
				pic: [],
				created_by: "",
				assets_general: [],
				assets: [],
				comparison_factor: [],
				unify_indicative_price_slug: "",
				composite_land_remaning_slug: "",
				planning_violation_price_slug: "",
				composite_land_remaning_value: "",
				planning_violation_price_value: "",
				certificate: {}
			},
			comparison_edit: [],
			comparison: COMPARISON,
			housingTypes: [],
			buildingRates: [],
			buildingCategories: [],
			buildingStructure: [],
			buildingAperture: [],
			buildingFactoryType: [],
			buildingCrane: [],
			propertyTypes: [],
			topographic: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			juridicals: [],
			materials: [],
			appraisersManager: [],
			appraisers: [],
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
			imageDescriptions: [],
			constructions: [],
			unifyIndicativePrice: [],
			compositeLandRemaning: [],
			planningViolationPrice: [],
			provinceName: null,
			districtName: null,
			wardName: null,
			streetName: null,
			assetName: null,
			full_address: null,
			addressName: {
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
			imageMap: null,
			imageMapDetail: null,
			full_address_noStreet: "",
			full_address_street: "",
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			current_create_by: "",
			status_text: "",
			status: 1,
			isStep3Active: false,
			checkRole: false,
			historyList: [],
			openPrint: false,
			reportData: null,
			jsonConfig: null,
			principleConfig: null,
			max_version: 1,
			createdBy: {},
			constructionRemainQuality: [],
			constructionPriceType: [],
			real_estate: {
				planning_info: "",
				planning_source: "",
				contact_person: "",
				contact_phone: ""
			}
		};
	},
	setup() {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const isMobile = ref(checkMobile());
		const isEdit = ref(false);
		const priceEstimateStore = usePriceEstimatesStore();
		priceEstimateStore.getDictionary();
		priceEstimateStore.getProvinces();
		const {
			priceEstimates,
			isSubmit,
			miscInfo,
			configThis,
			miscVariable
		} = storeToRefs(priceEstimateStore);

		const tempPriceEstimates = ref(priceEstimates.value);
		return {
			isMobile,
			priceEstimates,
			isSubmit,
			miscInfo,
			miscVariable,
			priceEstimateStore,
			configThis,
			isEdit,
			tempPriceEstimates
		};
	},
	async created() {
		const permission = this.$store.getters.currentPermissions;

		await permission.forEach(value => {
			if (value === "VIEW_CERTIFICATE_ASSET") {
				this.view = true;
			}
			if (value === "ADD_CERTIFICATE_ASSET") {
				this.add = true;
			}
			if (value === "EDIT_CERTIFICATE_ASSET") {
				this.edit = true;
			}
			if (value === "DELETE_CERTIFICATE_ASSET") {
				this.deleted = true;
			}
			if (value === "ACCEPT_CERTIFICATE_ASSET") {
				this.accept = true;
			}
		});

		this.configThis.toast = this.$toast;
		this.configThis.route = this.$route;
		this.configThis.router = this.$router;
		if (this.$route.query.id) {
			const response = await this.priceEstimateStore.getDataAllStep(
				this.$route.query.id
			);
			if (response.error) {
				this.$router.push({ name: "error.403" });
			} else {
				this.tempPriceEstimates = response.data;
			}
		} else {
			this.$router.push({ name: "error.403" });
		}
		if (!this.isMobile) {
			await this.$refs.wizard.tabs.forEach((tab, index) => {
				if (index <= this.miscVariable.step_active) {
					tab.checked = true;
				}
			});
			await this.$refs.wizard.changeTab(0, this.miscVariable.step_active);
			this.miscInfo.key_step_1 += 1;
			this.miscInfo.key_step_2 += 1;
			this.miscInfo.key_step_3 += 1;
		}
	},
	methods: {
		getProfiles() {
			const profile = this.$store.getters.profile;
			this.checkRole =
				(this.createdBy && profile.data.user.id === this.createdBy.id) ||
				["ROOT_ADMIN", "SUB_ADMIN"].includes(profile.data.user.roles[0].name);
		},
		showHistoryDrawer() {
			this.visibleHistoryDrawer = true;
			this.getHistoryTimeline(this.tempPriceEstimates.id);
		},
		onHistoryDrawerClose() {
			this.visibleHistoryDrawer = false;
		},
		showAdditionalDrawer() {
			this.visibleAdditionalDrawer = true;
		},
		onAdditionalDrawerClose() {
			this.visibleAdditionalDrawer = false;
		},

		onCancel() {
			return this.$router.push({ name: "price_estimates.index" });
		},
		formatDateTime(value) {
			return moment(String(value)).format("HH:mm DD/MM/YYYY");
		},
		async getHistoryTimeline(id) {
			const res = await CertificateAsset.getHistoryTimeline(id);
			if (res.data) {
				this.historyList = res.data;
			} else if (res.error) {
				return this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		async handleEdit(step) {
			console.log(
				"runhere",
				this.tempPriceEstimates,
				this.tempPriceEstimates.id,
				step
			);
			await this.$router.push({
				name: "price_estimates.edit",
				query: { id: this.tempPriceEstimates.id },
				params: { step: step }
			});
		},
		async handlePrint() {
			const response = await this.priceEstimateStore.getDataAllStep(
				this.$route.query.id
			);
			if (response.error) {
				this.$toast.open({
					message: `${response.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				if (
					!this.priceEstimates.step_3 ||
					!this.priceEstimates.step_3.total_area ||
					this.priceEstimates.step_3.total_area.length === 0
				) {
					this.$toast.open({
						message:
							"Chưa thể in giá sơ bộ do chưa đủ thông tin. Vui lòng lưu giá trị tài sản trước khi in.",
						type: "error",
						position: "top-right"
					});
				}

				this.priceEstimates.assets = [];
				this.priceEstimates.totalLandPrice = 0;
				this.priceEstimates.totalTangibleAssetPrice = 0;
				for (
					let index = 0;
					index < this.priceEstimates.step_3.total_area.length;
					index++
				) {
					const element = this.priceEstimates.step_3.total_area[index];
					const temp = {
						description: "Phần diện tích PHQH",
						land_type_description: element.land_type_purpose
							? element.land_type_purpose.description
							: "",
						area: element.main_area,
						price: element.unit_price,
						total: element.total_price
					};
					this.priceEstimates.totalLandPrice += Number(element.total_price);
					this.priceEstimates.assets.push(temp);
				}

				if (
					this.priceEstimates.step_3.planning_area &&
					this.priceEstimates.step_3.planning_area.length > 0
				) {
					for (
						let index = 0;
						index < this.priceEstimates.step_3.planning_area.length;
						index++
					) {
						const element = this.priceEstimates.step_3.planning_area[index];
						const temp = {
							description: "Phần diện tích không PHQH",
							land_type_description: element.land_type_purpose
								? element.land_type_purpose.description
								: "",
							area: element.planning_area,
							price: element.unit_price,
							total: element.total_price
						};
						this.priceEstimates.totalLandPrice += Number(element.total_price);
						this.priceEstimates.assets.push(temp);
					}
				}
				if (
					this.priceEstimates.step_3.tangible_assets &&
					this.priceEstimates.step_3.tangible_assets.length > 0
				)
					this.priceEstimates.totalTangibleAssetPrice = this.priceEstimates.step_3.tangible_assets.reduce(
						(total, asset) => total + (asset.total_price || 0),
						0
					);

				this.priceEstimates.totalAllPrice =
					Number(this.priceEstimates.totalLandPrice) +
					Number(this.priceEstimates.totalTangibleAssetPrice);
				console.log(this.priceEstimates);
				this.openPrint = true;
			}

			// this.printEstimateAssetPrice();
		},
		async printEstimateAssetPrice() {
			let id = this.tempPriceEstimates.id ? this.tempPriceEstimates.id : "";
			const res = await CertificateAsset.postEstimateAssetPrice(id);
			if (res.data) {
				if (res.data.assets.length > 0) {
					this.reportData = res.data;
					this.openPrint = true;
				} else {
					this.$toast.open({
						message: "Chưa thể tính giá sơ bộ do chưa có TSSS.",
						type: "error",
						position: "top-right"
					});
				}
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			}
		},
		async validateSubmitStep3() {
			const isValid = await this.$refs.step_3.validate();
			if (isValid) {
				this.priceEstimateStore.validateSubmitStep3();
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		async moveToAppraise() {
			if (this.tempPriceEstimates.id) {
				const res = await PriceEstimateModel.moveToAppraise(
					this.tempPriceEstimates.id
				);
				if (res.data) {
					this.isSubmit = false;

					this.$toast.open({
						message: "Chuyển chính thức thành công sang TSTĐ: " + res.data.id,
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.tempPriceEstimates.appraise_id = res.data.id;
				} else if (res.error) {
					this.isSubmit = false;
					this.$toast.open({
						message: `${res.error.message}`,
						type: "error",
						position: "top-right"
					});
				} else {
					this.isSubmit = false;
					this.$toast.open({
						message: "Lưu thất bại",
						type: "error",
						position: "top-right"
					});
				}
			} else {
				this.$toast.open({
					message: "Có lỗi xảy ra, vui lòng liên hệ để được hỗ trợ",
					type: "error",
					position: "top-right"
				});
			}
		},
		handleChange(prevIndex, nextIndex) {
			this.key_step_1 += 1;
			this.key_step_2 += 1;
			this.key_step_3 += 1;
			this.isStep3Active = !!(
				this.miscVariable.step_active === 3 && nextIndex === 2
			);
		},
		openMessage(
			message,
			type = "error",
			position = "top-right",
			duration = 3000
		) {
			this.$toast.open({
				message: message,
				type: type,
				position: position,
				duration: duration
			});
		}
	},
	async mounted() {},
	async beforeMount() {},
	computed: {
		isEditStatus() {
			// // console.log(this.form)
			let check = true;

			return check;
		}
	}
};
</script>

<style scoped lang="scss">
/deep/ .dropdown-item {
	min-width: unset !important;
	// padding: 0!important;
}

/deep/ .dropdown-menu.show {
	background: transparent !important;
	box-shadow: none !important;
}

/deep/ .dropdown-menu-right.show {
	background: transparent !important;
	box-shadow: none !important;
}

/deep/ .dropdown-menu {
	background: transparent !important;
	box-shadow: none !important;
}

/deep/ .dropup .dropdown-menu {
	background: transparent !important;
	box-shadow: none !important;
}
@media (max-width: 767px) {
	/deep/ .ant-timeline-item-content {
		margin-left: 25px;
		p {
			margin-bottom: 0.2em;
		}
	}
	/deep/ .ant-timeline-item-tail {
		border-left: 2px solid #26bf5fad;
	}

	/deep/ .ant-drawer-body {
		overflow: scroll;
		height: 86vh;
		padding-bottom: 0;
	}
	/deep/ .ant-drawer-content {
		height: 93vh;
		overflow: scroll;
	}
}
.certification-asset {
	@media (max-width: 449px) {
		margin-bottom: 100px;
	}
	.step7 {
		/deep/ .wizard-tab-content {
			padding: 5px 5px 40px 0px !important;
		}
	}
}
.height_form_wizard {
	@media (max-height: 660px) {
		height: 72vh !important;
	}
	@media (max-height: 800px) and (min-height: 660px) {
		height: 76vh !important;
	}
	@media (max-height: 970px) and (min-height: 800px) {
		height: 83vh !important;
	}
}
.btn {
	&-extra {
		min-width: 2.25rem;
	}
	&-history {
		position: fixed;
		right: 0;
		top: 170px;
		z-index: 100;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
	&-print {
		position: fixed;
		right: 0;
		top: 120px;
		z-index: 50;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
	&-additional {
		position: fixed;
		right: 0;
		top: 220px;
		z-index: 50;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
	&-drawer-footer {
		position: absolute !important;
	}
}

.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
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
	.btn_dropdown {
		border: white;
		border-radius: 5px;
		height: 35px;
		@media (max-width: 767px) {
			margin-top: 10px;
		}
	}
	.btn_group {
		@media (max-width: 767px) {
			width: 100%;
		}
		/deep/ .btn-secondary {
			background-image: none;
			border-color: none !important;
			box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		}
	}
}
</style>
