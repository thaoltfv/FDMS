<template>
	<div
		class="container-fluid"
		:style="isMobile() ? { margin: '0', padding: '0' } : {}"
	>
		<div v-if="!isMobile()" class="certification-asset">
			<form-wizard
				:key="key_render_formwizard"
				ref="wizard"
				color="#99D161"
				:title="`TSTD${idData ? `_${idData}` : ''}`"
				:subtitle="status_text"
				layout="vertical"
				finish-button-text="Hoàn Thành"
				back-button-text="Trở lại"
				next-button-text="Lưu"
				:startIndex="step_active || 0"
				@on-change="handleChange"
				class="vertical-steps steps-transparent"
				:class="{ step7: isStep7Active }"
			>
				<div>
					<button
						class="btn btn-orange btn-print btn-extra"
						@click="handlePrint"
					>
						<!-- <font-awesome-icon icon="print" /> -->
						<img src="@/assets/icons/ic_printer_white.svg" alt="history" />
					</button>
					<button
						class="btn btn-orange btn-history btn-extra"
						@click="showHistoryDrawerDrawer"
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
				<div class="wizard-custom-info-apartment" v-if="createdBy">
					<div class="col-13">
						<div class="row d-flex">
							<p class="mb-1">Version :</p>
							<p class="mb-1">{{ max_version }}</p>
						</div>
						<div class="row d-flex">
							<p class="mb-1">Mã HSTĐ :</p>
							<a
								class="mb-1"
								:href="`/certification_brief/detail?id=${certificate.id}`"
								v-if="certificate"
								target="_blank"
								>{{ certificate ? certificate.id : "" }}</a
							>
							<p class="mb-1" v-else>{{ certificate ? certificate.id : "" }}</p>
						</div>
						<div class="">
							<p class="mb-1">Người được chỉnh sửa :</p>
							<div>
								<p class="mb-1">- {{ createdBy.name }}</p>
							</div>
						</div>
					</div>
				</div>
				<tab-content title="Thông tin tài sản" icon="">
					<ValidationObserver
						tag="div"
						ref="step_1"
						@submit.prevent="validateSubmitStep1"
					>
						<Step1
							:isEdit="isEdit"
							:data="form.step_1"
							:key="key_step_1"
							:propertyTypes="propertyTypes"
							:provinces="provinces"
							:districts="districts"
							:wards="wards"
							:streets="streets"
							:full_address="full_address"
							:projects="projects"
							:blocks="blocks"
							:floors="floors"
							:apartments="apartments"
							:directions="directions"
							:furniture_list="furniture_list"
							:loai_can_ho="loai_can_ho"
							:basic_utilities="basic_utilities"
							:imageDescriptions="imageDescriptions"
							@getDistrict="changeProvince"
							@getWardStreet="changeDistrict"
							@getWard="changeWard"
							@changeStreet="changeStreet"
							@getAssetType="changeAssetType"
							@handleChangeProject="handleChangeProject"
							@handleChangeBlock="handleChangeBlock"
							@handleChangeFloor="handleChangeFloor"
						/>
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
									v-if="edit || add"
									class="btn btn-white btn-orange text-nowrap"
									@click.prevent="duplicateCertificateAsset"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_save.svg"
										style="margin-right: 12px"
										alt="save"
									/>Nhân bản
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
								<button
									v-if="isEditStatus && isCancelEnable"
									@click.prevent="handleCancelProperty()"
									class="btn btn-white text-nowrap"
								>
									<img
										src="@/assets/icons/ic_destroy.svg"
										style="margin-right: 12px"
										alt="cancel"
									/>Hủy tài sản
								</button>
							</div>
						</div>
					</ValidationObserver>
				</tab-content>

				<tab-content title="Pháp lý tài sản" icon="">
					<ValidationObserver
						tag="form"
						ref="step_2"
						class="height_form_wizard"
						@submit.prevent="validateSubmitStep2"
					>
						<Step2
							:data="form.step_2"
							:key="key_step_2"
							:juridicals="juridicals"
							:provinceName="provinceName"
							:full_address="full_address"
							@updateLegal="updateLegal"
							@createLegal="createLegal"
							@deleteLegal="deleteLegal"
						/>
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
									v-if="edit || add"
									class="btn btn-white btn-orange text-nowrap"
									@click.prevent="duplicateCertificateAsset"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_save.svg"
										style="margin-right: 12px"
										alt="save"
									/>Nhân bản
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
								<button
									v-if="isEditStatus && isCancelEnable"
									@click.prevent="handleCancelProperty()"
									class="btn btn-white text-nowrap"
								>
									<img
										src="@/assets/icons/ic_destroy.svg"
										style="margin-right: 12px"
										alt="cancel"
									/>Hủy tài sản
								</button>
							</div>
						</div>
					</ValidationObserver>
				</tab-content>

				<tab-content title="Cơ sở thẩm định" icon="">
					<ValidationObserver
						tag="div"
						ref="step_3"
						class="height_form_wizard"
						@submit.prevent="validateSubmitStep3"
					>
						<Step3
							:data="form.step_3"
							:key="key_step_3"
							:appraisalFacility="appraisalFacility"
							:approach="approach"
							:methodsUsed="methodsUsed"
							:appraisalPrinciples="appraisalPrinciples"
							:unifyIndicativePrice="unifyIndicativePrice"
							:compositeLandRemaning="compositeLandRemaning"
							:planningViolationPrice="planningViolationPrice"
							@changeLandRemaing="changeLandRemaing"
							@changeViolationPrice="changeViolationPrice"
							@changePercentRemain="changePercentRemain"
							@changePercentVio="changePercentVio"
						/>
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
									v-if="edit || add"
									class="btn btn-white btn-orange text-nowrap"
									@click.prevent="duplicateCertificateAsset"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_save.svg"
										style="margin-right: 12px"
										alt="save"
									/>Nhân bản
								</button>
								<button
									v-if="isEditStatus"
									class="btn btn-white"
									@click.prevent="handleEdit(2)"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_edit.svg"
										style="margin-right: 12px"
										alt="save"
									/>Chỉnh sửa
								</button>
								<button
									v-if="isEditStatus && isCancelEnable"
									@click.prevent="handleCancelProperty()"
									class="btn btn-white text-nowrap"
								>
									<img
										src="@/assets/icons/ic_destroy.svg"
										style="margin-right: 12px"
										alt="cancel"
									/>Hủy tài sản
								</button>
							</div>
						</div>
					</ValidationObserver>
				</tab-content>

				<tab-content title="Tài sản so sánh" icon="">
					<ValidationObserver tag="div" ref="step_4">
						<Step4
							:data="form.step_4"
							:key="key_step_4"
							:step_active="step_active"
							:comparison="comparison"
							:propertyTypes="propertyTypes"
							:type_purposes="type_purposes"
							:coordinates="form.step_1.coordinates"
							:distance_max="distance_max"
							@choosingAsset="choosingAsset"
							@saveImageMap="saveImageMap"
							@changeDistance="changeDistances"
						/>
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
									class="btn btn-white"
									@click.prevent="handleEdit(3)"
									type="submit"
								>
									<img
										src="@/assets/icons/ic_edit.svg"
										style="margin-right: 12px"
										alt="save"
									/>Chỉnh sửa
								</button>
								<button
									v-if="isEditStatus && isCancelEnable"
									@click.prevent="handleCancelProperty()"
									class="btn btn-white text-nowrap"
								>
									<img
										src="@/assets/icons/ic_destroy.svg"
										style="margin-right: 12px"
										alt="cancel"
									/>Hủy tài sản
								</button>
							</div>
						</div>
					</ValidationObserver>
				</tab-content>

				<tab-content title="Giá trị tài sản" icon="">
					<ValidationObserver
						tag="div"
						ref="step_5"
						@submit.prevent="validateSubmitStep5"
					>
						<Step5
							:data="this.form.step_5"
							:idData="idData"
							:key="key_step_5"
							:jsonConfig="jsonConfig"
							:isEditStatus="isEditStatus"
						/>
					</ValidationObserver>
				</tab-content>
			</form-wizard>
			<ModalNotificationAppraisal
				v-if="openCancelAppraisal"
				@cancel="openCancelAppraisal = false"
				v-bind:notification="message"
				@action="handleActionCancelAppraise"
			/>
			<ModalNotificationAppraisal
				v-if="showConfirmEdit"
				@cancel="showConfirmEdit = false"
				v-bind:notification="messageConfirm"
				@action="confirmEditStep"
			/>
			<ModalNotificationAppraisal
				v-if="showConfirmDuplicate"
				@cancel="showConfirmDuplicate = false"
				:notification="'Bạn có muốn nhân bản tài sản thẩm định không'"
				@action="actionDuplicate"
			/>
			<ModalPrintEstimateAssetApartment
				v-if="openPrint"
				@cancel="openPrint = false"
				:data="reportData"
			/>
		</div>
		<div v-else class="certification-asset" style="margin-bottom: 190px;">
			<div>
				<button class="btn btn-orange btn-print btn-extra" @click="handlePrint">
					<!-- <font-awesome-icon icon="print" /> -->
					<img src="@/assets/icons/ic_printer_white.svg" alt="history" />
				</button>
				<button
					class="btn btn-orange btn-history btn-extra"
					@click="showHistoryDrawerDrawer"
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
			<ValidationObserver
				tag="div"
				ref="step_1"
				@submit.prevent="validateSubmitStep1"
			>
				<Step1
					:isEdit="isEdit"
					:data="form.step_1"
					:key="key_step_1"
					:propertyTypes="propertyTypes"
					:provinces="provinces"
					:districts="districts"
					:wards="wards"
					:streets="streets"
					:full_address="full_address"
					:projects="projects"
					:blocks="blocks"
					:floors="floors"
					:apartments="apartments"
					:directions="directions"
					:furniture_list="furniture_list"
					:basic_utilities="basic_utilities"
					:imageDescriptions="imageDescriptions"
					@getDistrict="changeProvince"
					@getWardStreet="changeDistrict"
					@getWard="changeWard"
					@changeStreet="changeStreet"
					@getAssetType="changeAssetType"
					@handleChangeProject="handleChangeProject"
					@handleChangeBlock="handleChangeBlock"
					@handleChangeFloor="handleChangeFloor"
				/>
				<!-- <div class="btn-footer d-md-flex d-block" style="bottom: 60px;padding-top: 0px;padding-bottom: 10px;"> -->
				<div class="btn-footer row" style="">
					<div class="col-6">
						<button
							@click.prevent="handleChangeBack"
							class="btn btn-white text-nowrap"
							style="width: fit-content;"
						>
							<img
								src="@/assets/icons/ic_cancel.svg"
								style="margin-right: 12px"
								alt="save"
							/>Thoát
						</button>
					</div>
					<div class="col-6" style="text-align: right;">
						<b-dropdown class="btn_dropdown" no-caret right dropup>
							<template #button-content>
								<button
									style="margin-right: 2px"
									class="btn btn-white"
									type="button"
								>
									<img
										class="img"
										src="@/assets/icons/ic_more.svg"
										alt="cancel"
									/>Hành động
								</button>
							</template>
							<b-dropdown-item
								v-if="edit || add"
								style="margin-left: 55px;width: 150px;padding: 0;"
								class="btn btn-white btn-orange text-nowrap"
								@click.prevent="duplicateCertificateAsset"
								type="submit"
							>
								<div class="div_item_dropdown">
									<img
										src="@/assets/icons/ic_duplicate.svg"
										style="margin-right: 12px; height: 1.25rem"
										alt="save"
									/>
									<span style="font-size: 13px;">Nhân bản</span>
								</div>
							</b-dropdown-item>
							<b-dropdown-item
								v-if="isEditStatus"
								style="margin-left: 55px;width: 150px;padding: 0;"
								class="btn btn-white"
								@click.prevent="handleEdit(0)"
								type="submit"
							>
								<div class="div_item_dropdown">
									<img
										src="@/assets/icons/ic_edit.svg"
										style="margin-right: 12px; height: 1.25rem"
										alt="save"
									/>
									<span style="font-size: 13px;">Chỉnh sửa</span>
								</div>
							</b-dropdown-item>
							<b-dropdown-item
								v-if="isEditStatus && isCancelEnable"
								style="margin-left: 55px;width: 150px;padding: 0;"
								class="btn btn-white text-nowrap"
								@click.prevent="handleCancelProperty()"
							>
								<div class="div_item_dropdown">
									<img
										src="@/assets/icons/ic_destroy.svg"
										style="margin-right: 12px; height: 1.25rem"
										alt="cancle"
									/>
									<span style="font-size: 13px;">Hủy tài sản</span>
								</div>
							</b-dropdown-item>
						</b-dropdown>
					</div>
				</div>
				<!-- </div> -->
			</ValidationObserver>
			<ModalNotificationAppraisal
				v-if="openCancelAppraisal"
				@cancel="openCancelAppraisal = false"
				v-bind:notification="message"
				@action="handleActionCancelAppraise"
			/>
			<ModalNotificationAppraisal
				v-if="showConfirmEdit"
				@cancel="showConfirmEdit = false"
				v-bind:notification="messageConfirm"
				@action="confirmEditStep"
			/>
			<ModalNotificationAppraisal
				v-if="showConfirmDuplicate"
				@cancel="showConfirmDuplicate = false"
				:notification="'Bạn có muốn nhân bản tài sản thẩm định không'"
				@action="actionDuplicate"
			/>
			<ModalPrintEstimateAssetApartment
				v-if="openPrint"
				@cancel="openPrint = false"
				:data="reportData"
			/>
		</div>
	</div>
</template>

<script>
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup,
	BCard,
	BRow,
	BCol,
	BFormGroup,
	BFormInput
} from "bootstrap-vue";
import { FormWizard, TabContent } from "vue-form-wizard";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import "vue-form-wizard/dist/vue-form-wizard.min.css";
import Step1 from "./componentDetail/Step1";
import Step2 from "./componentDetail/Step2";
import Step3 from "./componentDetail/Step3";
import Step4 from "./componentDetail/Step4";
import Step5 from "./componentDetail/Step5";
// import Step7 from './component/Step7'
import ModalNotificationAppraisal from "@/components/Modal/ModalNotificationAppraisal";
import WareHouse from "@/models/WareHouse";
import Certificate from "@/models/Certificate";
import { COMPARISON_APARTMENT } from "@/enum/comparison-factor-apartment.enum";
import AppraiseData from "@/models/AppraiseData";
import CertificateAsset from "@/models/CertificateAsset";
import moment from "moment";
import { Timeline, Drawer } from "ant-design-vue";
import ModalPrintEstimateAssetApartment from "@/components/Modal/ModalPrintEstimateAssetApartment";
const jsonConfig = require("../../../../../config/workflow.json");

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
		BRow,
		BCol,
		BFormGroup,
		BFormInput,
		InputText,
		InputTextarea,
		Step1,
		Step2,
		Step3,
		Step4,
		Step5,
		// Step7,
		ModalNotificationAppraisal,
		Timeline,
		Drawer,
		ModalPrintEstimateAssetApartment
	},
	data() {
		return {
			isAutomation: false,
			isEdit: false,
			idData: null,
			isSave: false,
			openNotification: false,
			isSubmit: false,
			isCancelEnable: true,
			openCancelAppraisal: false,
			showConfirmEdit: false,
			showConfirmDuplicate: false,
			showDetailPlanning: true,
			step_edit: "",
			message: "",
			messageConfirm: "",
			visibleAdditionalDrawer: false,
			visibleHistoryDrawer: false,
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
			key_render_formwizard: 70000,
			isHaveContruction: false,
			step_active: null,
			distance_max: null,
			form: {
				step_1: {
					asset_type_id: "",
					province_id: "",
					district_id: "",
					ward_id: "",
					street_id: "",
					coordinates: "",
					appraise_asset: "",
					apartment_asset_properties: {
						handover_year: "",
						project_id: "",
						block_id: "",
						floor_id: "",
						apartment_id: "",
						legal_id: "",
						floor: "",
						area: "",
						bedroom_num: "",
						wc_num: "",
						description: "",
						direction_id: "",
						furniture_quality_id: "",
						utilities: []
					},
					pic: [],
					full_address: "",
					real_estate: {
						planning_info: "",
						planning_source: "",
						contact_person: "",
						contact_phone: ""
					}
				},
				step_2: {
					law: []
				},
				step_3: {
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
						description: "- Giả thiết:\n- Giả thiết đặc biệt:",
						approach_id: "",
						basis_property_id: "",
						principle_id: "",
						method_used_id: ""
					}
				},
				step_4: {
					comparison_factor: ["phap_ly"],
					assets_general: [],
					map_img: ""
				},
				step_5: {},
				status: 1,
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
				created_by: "",
				assets_general: [],
				assets: [],
				comparison_factor: [],
				unify_indicative_price_slug: "",
				composite_land_remaning_slug: "",
				planning_violation_price_slug: "",
				composite_land_remaning_value: "",
				planning_violation_price_value: ""
			},
			comparison_edit: [],
			comparison: COMPARISON_APARTMENT,
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
			imageDescriptions: [],
			constructions: [],
			unifyIndicativePrice: [],
			compositeLandRemaning: [],
			planningViolationPrice: [],
			projects: [],
			blocks: [],
			floors: [],
			apartments: [],
			basic_utilities: [],
			directions: [],
			furniture_list: [],
			loai_can_ho: [],
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
			sentRequest: false,
			isStep7Active: false,
			isDuplicate: false,
			createdBy: {},
			max_version: "",
			certificate: {},
			jsonConfig: jsonConfig,
			principleConfig: jsonConfig.principle,
			checkRole: false,
			status: 1,
			historyList: [],
			openPrint: false,
			reportData: [],
			real_estate: {
				planning_info: "",
				planning_source: "",
				contact_person: "",
				contact_phone: ""
			}
		};
	},
	beforeRouteEnter: async (to, from, next) => {
		if (to.query["id"]) {
			await CertificateAsset.getAllStepApartment(to.query["id"])
				.then(resp => {
					if (resp.data) {
						to.meta["step"] = resp.data;
						return next();
					} else {
						if (resp.error && resp.error.statusCode) {
							return next("/".resp.error.statusCode);
						}
					}
				})
				.catch(() => {
					return next("/403");
				});

			// const res = await CertificateAsset.getAllDataApartment(to.query['id'])
			// await // console.log(res, 'res')
		}
		return next();
	},
	async created() {
		const permission = this.$store.getters.currentPermissions;
		await WareHouse.getProvince().then(resp => {
			this.provinces = resp.data;
		});
		// fix_permission
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
		if (
			"id" in this.$route.query &&
			this.$route.name === "certification_asset.apartment.detail"
		) {
			this.isEdit = true;
			if (this.$route.meta["step"]) {
				let bindDataStep = this.$route.meta["step"];
				this.form.created_by = bindDataStep.created_by.id;
				this.createdBy = bindDataStep.created_by;
				this.max_version = bindDataStep.max_version;
				this.certificate = bindDataStep.certificate;
				this.real_estate = bindDataStep.real_estate;
				this.status = bindDataStep.status;
				if (bindDataStep.certificate) {
					this.isCancelEnable = false;
				}
				// step 1
				if (bindDataStep.pic) {
					this.form.step_1.pic = bindDataStep.pic;
				}
				if (bindDataStep.apartment_asset_properties) {
					this.form.step_1.apartment_asset_properties =
						bindDataStep.apartment_asset_properties;
				}
				if (bindDataStep.appraise_asset) {
					this.form.step_1.appraise_asset = bindDataStep.appraise_asset;
				}
				if (bindDataStep.asset_type_id) {
					this.form.step_1.asset_type_id = bindDataStep.asset_type_id;
				}
				if (bindDataStep.coordinates) {
					this.form.step_1.coordinates = bindDataStep.coordinates;
				}
				if (bindDataStep.province_id) {
					this.form.step_1.province_id = bindDataStep.province_id;
				}
				if (bindDataStep.district_id) {
					this.form.step_1.district_id = bindDataStep.district_id;
				}
				if (bindDataStep.ward_id) {
					this.form.step_1.ward_id = bindDataStep.ward_id;
				}
				if (bindDataStep.street_id) {
					this.form.step_1.street_id = bindDataStep.street_id;
				}
				if (bindDataStep.project_id) {
					this.form.step_1.project_id = bindDataStep.project_id;
				}
				if (bindDataStep.full_address) {
					this.form.step_1.full_address = bindDataStep.full_address;
				}
				if (bindDataStep.real_estate) {
					this.form.step_1.real_estate = bindDataStep.real_estate;
				}
				// step 2
				if (bindDataStep.law && bindDataStep.law.length > 0) {
					this.form.step_2.law = bindDataStep.law;
				}
				// step 3
				if (bindDataStep.appraisal_methods) {
					this.form.step_3.appraisal_methods = bindDataStep.appraisal_methods;
				}
				if (bindDataStep.value_base_and_approach) {
					this.form.step_3.value_base_and_approach =
						bindDataStep.value_base_and_approach;
				}
				// step 4
				if (
					bindDataStep.comparison_factors &&
					bindDataStep.comparison_factors.length > 0
				) {
					this.form.step_4.comparison_factor = bindDataStep.comparison_factors;
				}
				if (
					bindDataStep.assets_general &&
					bindDataStep.assets_general.length > 0
				) {
					this.form.step_4.assets_general = bindDataStep.assets_general;
				}
				if (bindDataStep.map_img) {
					this.form.step_4.map_img = bindDataStep.map_img;
				}
				if (bindDataStep.distance_max) {
					this.distance_max = bindDataStep.distance_max;
				}
				// step 5
				if (
					bindDataStep.assets_general &&
					bindDataStep.assets_general.length > 0
				) {
					this.form.step_5 = { ...this.$route.meta["step"] };
				}
				this.status_text = bindDataStep.status_text;
				this.form.status = bindDataStep.status;
				this.idData = await bindDataStep.id;
				if (this.form.step_1.asset_type_id === 38) {
					this.isHaveContruction = true;
				} else {
					this.isHaveContruction = false;
				}
				if (bindDataStep.step >= 4) {
					this.step_active = 5;
				} else this.step_active = bindDataStep.step;
				if (!this.isMobile()) {
					await this.$refs.wizard.tabs.forEach((tab, index) => {
						if (index <= this.step_active - 1) {
							tab.checked = true;
						}
					});
					await this.$refs.wizard.changeTab(0, this.step_active - 1);
				}
			}
			// if (this.$route.meta['step7']) { this.form.step_7 = Object.assign(this.form.step_7, { ...this.$route.meta['step7'] }) }
			// if (this.form.step_7.construction_company && this.form.step_7.construction_company.length > 0) {
			// 	await this.form.step_7.tangible_assets[0].construction_company.forEach(item => {
			// 		this.form.construction_company_ids.push(item.construction_company_id)
			// 	})
			// }
			if (this.form.step_4.comparison_factor.length > 1) {
				this.comparison.forEach(item => {
					this.form.step_4.comparison_factor.forEach(itemFactor => {
						if (item.slug === itemFactor && item.visible === false) {
							item.visible = true;
						}
					});
				});
			}
			this.key_step_1 += 1;
			this.key_step_2 += 1;
			this.key_step_3 += 1;
			this.key_step_4 += 1;
			this.key_step_5 += 1;
			if (!this.isMobile()) {
				if (this.$route.params.step || this.$route.params.step === 0) {
					await this.$refs.wizard.changeTab(0, this.$route.params.step);
				}
			}
			if (this.step_active < 4) {
				this.isAutomation = true;
			}
		} else if (
			"id" in this.$route.params &&
			this.$route.name === "certification_asset.apartment.create"
		) {
			if (this.$route.meta["step"]) {
				let bindDataStep = this.$route.meta["step"];
				this.isDuplicate = true;
				// step 1
				if (bindDataStep.economic_infomation) {
					this.form.step_1.economic_infomation =
						bindDataStep.economic_infomation;
				}
				if (bindDataStep.general_infomation) {
					this.form.step_1 = bindDataStep.general_infomation;
				}
				if (bindDataStep.traffic_infomation) {
					this.form.step_1.traffic_infomation = bindDataStep.traffic_infomation;
				}
				if (bindDataStep.pic && bindDataStep.pic.length > 0) {
					this.form.step_1.pic = bindDataStep.pic;
				}
				// step 2
				if (bindDataStep.law && bindDataStep.law.length > 0) {
					this.form.step_2.law = bindDataStep.law;
				}
				// step 3
				if (bindDataStep.appraisal_methods) {
					this.form.step_3.appraisal_methods = bindDataStep.appraisal_methods;
				}
				if (bindDataStep.value_base_and_approach) {
					this.form.step_3.value_base_and_approach =
						bindDataStep.value_base_and_approach;
				}
				// step 4

				// step 5
			}
			if (this.form.step_1.asset_type_id === 38) {
				this.isHaveContruction = true;
			} else {
				this.isHaveContruction = false;
			}
			this.status_text = "Mới";
			this.status = 1;
			this.key_step_1 += 1;
			this.key_step_2 += 1;
			this.key_step_3 += 1;
			this.key_step_4 += 1;
			this.key_step_5 += 1;
		}
		this.getProfiles();
		this.getProvinces();
		// this.getProjects()
	},

	methods: {
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
		async handleEdit(step) {
			await this.$router.push({
				name: "certification_asset.apartment.edit",
				query: { id: this.idData },
				params: { step: step }
			});
		},
		duplicateCertificateAsset() {
			this.$router
				.push({
					name: "certification_asset.apartment.create",
					params: { id: this.idData }
				})
				.catch(_ => {});
		},
		async handleChange(prevIndex, nextIndex) {
			if (nextIndex === 3 && this.isAutomation) {
				// const response = await CertificateAsset.getAssetAutomationStep6(this.idData)
				// if (response.data) {
				// 	this.form.step_4.assets_general = await response.data.assets
				// 	this.form.step_4.comparison_factor = await response.data.comparison_factor
				// 	this.form.step_4.map_img = ''
				// 	this.distance_max = response.data.distance_max
				// 	if (response.data.assets.length === 0) {
				// 		await this.$toast.open({
				// 			message: `${response.data.message}`,
				// 			type: 'success',
				// 			position: 'top-right',
				// 			duration: 6000
				// 		})
				// 	} else {
				// 		await this.$toast.open({
				// 			message: `Đã tìm được ${response.data.assets.length} tài sản so sánh`,
				// 			type: 'success',
				// 			position: 'top-right',
				// 			duration: 6000
				// 		})
				// 	}
				// 	this.isAutomation = false
				// }
			}
			if (nextIndex + 1 === 1) {
				this.key_step_1 += 1;
			}
			if (nextIndex + 1 === 2) {
				this.key_step_2 += 1;
			}
			if (nextIndex + 1 === 4) {
				this.key_step_4 += 1;
			}
			this.isStep7Active = !!(this.step_active === 7 && nextIndex === 6);
		},
		confirmSavePreviousStep(step_edit) {
			this.step_edit = step_edit;
			if (this.step_active > step_edit) {
				this.showConfirmEdit = true;
				this.messageConfirm = `Dữ liệu ở sau bước ${step_edit} sẽ phải xác nhận lại. Bạn vẫn muốn tiếp tục ?`;
			} else {
				this.confirmEditStep();
			}
		},
		confirmEditStep() {
			let step = this.step_edit;
			if (step === 1) {
				this.handleSubmitStep_1(this.form.step_1, this.idData);
			} else if (step === 2) {
				this.handleSubmitStep_2(this.form.step_2, this.idData);
			} else if (step === 3) {
				this.handleSubmitStep_3(this.form.step_3, this.idData);
			} else if (step === 4) {
				this.handleSubmitStep_4(this.form.step_4, this.idData);
			} else if (step === 5) {
			}
			if (step < 4) {
				this.isAutomation = true;
			}

			this.step_active = step;
			this.showConfirmEdit = false;
		},
		async handleChangeBack() {
			// this.$refs.wizard.prevTab()
			return this.$router.go(-1);
		},
		// ------------------------------------------ Ation of STEP 1------------------------------------------------------------//
		async validateSubmitStep1() {
			const isValid = await this.$refs.step_1.validate();
			if (isValid) {
				if (
					"id" in this.$route.query &&
					this.$route.name === "certification_asset.apartment.edit"
				) {
					this.confirmSavePreviousStep(1);
				} else if (
					this.$route.name === "certification_asset.apartment.create"
				) {
					if (this.isDuplicate) {
						this.showConfirmDuplicate = true;
					} else {
						this.confirmSavePreviousStep(1);
					}
				}
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		actionDuplicate() {
			this.handleSubmitStep_1(this.form.step_1, this.idData ? this.idData : "");
			this.isDuplicate = false;
		},
		async handleSubmitStep_1(dataStep1) {
			this.isSubmit = true;
			let id = this.idData ? this.idData : "";
			const res = await CertificateAsset.submitApartmentStep1(dataStep1, id);
			if (res.data) {
				this.isSubmit = false;
				this.idData = res.data.id;
				// this.form.step_2.land_details.coordinates = res.data.general_infomation.coordinates
				this.$toast.open({
					message: "Lưu thông tin chung thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				this.$refs.wizard.maxStep = 1;
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 1) {
						tab.checked = false;
					}
				});
				this.key_step_2 += 1;
				await this.$refs.wizard.nextTab();
				this.status_text = "Mới";
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
		},
		addTurning() {
			this.form.step_1.traffic_infomation.property_turning_time.push({
				is_alley_with_connection: "",
				main_road_distance: "",
				main_road_length: "",
				material_id: "",
				turning:
					"Hẻm số " +
					(this.form.step_1.traffic_infomation.property_turning_time.length + 1)
			});
		},
		deleteTurning(index) {
			this.form.step_1.traffic_infomation.property_turning_time.splice(
				index,
				1
			);
		},
		changeRoadDistance(value, index) {
			this.form.step_1.traffic_infomation.property_turning_time[
				index
			].main_road_distance = value;
		},
		changeRoadAlley(value, index) {
			this.form.step_1.traffic_infomation.property_turning_time[
				index
			].main_road_length = value;
		},
		changeDescriptionFrontSide(description) {
			this.form.step_1.traffic_infomation.description = description;
		},
		uploadImage(image) {
			this.form.step_1.pic.push(image);
			this.key_step_1 += 1;
		},
		// --------------------------------------------- Ation of STEP 2------------------------------------------------------------//
		createLegal(dataLegal) {
			this.form.step_2.law.push(dataLegal);
			this.$toast.open({
				message: "Thêm thành công",
				type: "success",
				position: "top-right",
				duration: 3000
			});
		},
		deleteLegal(index) {
			this.form.step_2.law.splice(index, 1);
			this.$toast.open({
				message: "Xóa thành công",
				type: "success",
				position: "top-right",
				duration: 3000
			});
		},
		updateLegal(dataEdit, index) {
			this.form.step_2.law[index] = dataEdit;
			this.$toast.open({
				message: "Cập nhật thành công",
				type: "success",
				position: "top-right",
				duration: 3000
			});
		},
		async validateSubmitStep2() {
			const isValid = await this.$refs.step_2.validate();
			if (isValid) {
				if (this.form.step_2.law.length === 0) {
					this.$toast.open({
						message: "Vui lòng thêm pháp lý cho tài sản",
						type: "error",
						position: "top-right"
					});
				} else {
					this.confirmSavePreviousStep(2);
				}
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		async handleSubmitStep_2(dataStep2) {
			this.isSubmit = true;
			const res = await CertificateAsset.submitApartmentStep2(
				dataStep2,
				this.idData
			);
			if (res.data) {
				this.isSubmit = false;
				this.$toast.open({
					message: "Lưu pháp lý tài sản thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				if (res.data.appraisal_methods && res.data.value_base_and_approach) {
					this.form.step_3.appraisal_methods = res.data.appraisal_methods;
					this.form.step_3.value_base_and_approach =
						res.data.value_base_and_approach;
				}
				if (!res.data.value_base_and_approach) {
					this.findAppraisalFacility();
					this.findAppraisalPrinciples();
					this.findApproach();
					this.findMethodsUsed();
				}
				this.key_step_5 += 1;
				this.$refs.wizard.maxStep = 4;
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 4) {
						tab.checked = false;
					}
				});
				await this.$refs.wizard.nextTab();
				this.status_text = "Mới";
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
		},

		// --------------------------------------------- Ation of STEP 3------------------------------------------------------------//
		changeLandRemaing(event) {
			if (event === "theo-ty-le-gia-dat-co-so-chinh") {
				this.form.step_3.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = 50;
			} else {
				this.form.step_3.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value =
					"";
			}
			this.key_step_5 += 1;
		},
		changeViolationPrice(event) {
			if (event === "theo-ty-le-gia-dat-thi-truong") {
				this.form.step_3.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = 80;
			} else {
				this.form.step_3.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value =
					"";
			}
			this.key_step_5 += 1;
		},
		changePercentRemain(event) {
			this.form.step_3.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = event;
		},
		changePercentVio(event) {
			this.form.step_3.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = event;
		},
		async validateSubmitStep3() {
			const isValid = await this.$refs.step_3.validate();
			if (isValid) {
				this.confirmSavePreviousStep(3);
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		async handleSubmitStep_3(dataStep3) {
			this.isSubmit = true;
			const res = await CertificateAsset.submitApartmentStep3(
				dataStep3,
				this.idData
			);
			if (res.data) {
				this.isSubmit = false;
				this.$toast.open({
					message: "Lưu cơ sở thẩm định thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				this.$refs.wizard.maxStep = 3;
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 3) {
						tab.checked = false;
					}
				});
				await this.$refs.wizard.nextTab();
				this.step_active = 3;
				this.status_text = "Mới";
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
		},
		// --------------------------------------------- Ation of STEP 4------------------------------------------------------------//
		choosingAsset(assets) {
			this.form.step_4.assets_general = assets;
			this.form.step_4.map_img = "";
		},
		saveImageMap(link) {
			this.form.step_4.map_img =
				"https://apod.nasa.gov/apod/image/1505/AuroraNorway_Richardsen_2330.jpg";
		},
		changeDistances(distance_max) {
			this.distance_max = distance_max;
		},
		async validateSubmitStep4() {
			const isValid = await this.$refs.step_4.validate();
			let step_4 = this.form.step_4;
			if (isValid) {
				if (step_4.comparison_factor.length === 0) {
					this.$toast.open({
						message: "Vui lòng nhập yếu tố so sánh",
						type: "error",
						position: "top-right"
					});
				} else if (step_4.assets_general.length === 0) {
					this.$toast.open({
						message: "Vui lòng chọn tài sản so sánh",
						type: "error",
						position: "top-right"
					});
				} else if (step_4.assets_general.length > 3) {
					this.$toast.open({
						message: "Chỉ được chọn tối đa 3 tài sản so sánh",
						type: "error",
						position: "top-right"
					});
				} else if (step_4.assets_general.length < 3) {
					this.$toast.open({
						message: "Vui lòng chọn đủ 3 tài sản so sánh",
						type: "error",
						position: "top-right"
					});
				} else if (!step_4.map_img) {
					this.$toast.open({
						message: "Vui lòng chụp hình ảnh",
						type: "error",
						position: "top-right"
					});
				} else {
					this.confirmSavePreviousStep(4);
				}
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		async handleSubmitStep_4(dataStep4) {
			this.isSubmit = true;
			const res = await CertificateAsset.submitApartmentStep4(
				dataStep4,
				this.idData
			);
			if (res.data) {
				this.$toast.open({
					message: "Lưu lựa chọn tài sản so sánh thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				this.distance_max = res.data.distance_max;
				await this.$router.push({
					name: "certification_asset.apartment.detail",
					query: { id: this.idData },
					params: { step: 5 }
				});
				// const getDataStep7 = await CertificateAsset.getDataStep7(this.idData)
				// if (getDataStep7.data) {
				// 	this.form.step_7 = await getDataStep7.data
				// 	if (this.form.step_7.construction_company && this.form.step_7.construction_company.length > 0) {
				// 		this.form.construction_company_ids = []
				// 		this.form.step_7.construction_company.forEach(item => {
				// 			this.form.construction_company_ids.push(item.construction_company_id)
				// 		})
				// 	}
				// 	this.$refs.wizard.maxStep = 6
				// 	this.$refs.wizard.tabs.forEach((tab, index) => {
				// 		if (index > 6) {
				// 			tab.checked = false
				// 		}
				// 	})
				// 	await this.$refs.wizard.nextTab()
				// 	this.key_step_7 += 1
				// 	this.status_text = 'Đang thực hiện'
				// } else {
				// 	await this.$toast.open({
				// 		message: 'lấy dữ liệu thất bại',
				// 		type: 'error',
				// 		position: 'top-right'
				// 	})
				// }
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
			this.isSubmit = false;
		},

		// --------------------------------------------- Ation of STEP 7------------------------------------------------------------//
		async updateDataStep7() {
			// const getDataStep7 = await CertificateAsset.getDataStep7(this.idData)
			// if (getDataStep7.data) {
			//   this.form.step_7 = getDataStep7.data
			// }
			// this.key_step_7 += 1
		},
		makeID(length) {
			let result = "";
			let characters =
				"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			let charactersLength = characters.length;
			for (let i = 0; i < length; i++) {
				result += characters.charAt(
					Math.floor(Math.random() * charactersLength)
				);
			}
			return result;
		},
		handleSaveAppraiseLaw(data_law) {
			this.form.appraise_law = data_law;
		},
		changeUnifyIndicativePrice(event) {},
		changeCompositeLandRemaning(event) {
			if (event === "theo-ty-le-gia-dat-co-so-chinh") {
				this.form.composite_land_remaning_value = 80;
			} else {
				this.form.composite_land_remaning_value = "";
			}
		},
		changePlanningViolationPrice(event) {
			if (event === "theo-ty-le-gia-dat-thi-truong") {
				this.form.planning_violation_price_value = 50;
			} else {
				this.form.planning_violation_price_value = "";
			}
		},
		async getAppraiserId(id, version) {
			const res = await AppraiseData.getAppraiseID(id, version);
			if (typeof res.data !== "undefined") {
				if (
					"id" in this.$route.params &&
					this.$route.name === "certificate.create"
				) {
					this.form = res.data;
					this.form.id = null;
					this.form.status = 1;
				} else this.form = res.data;
				this.data.assets = this.form.assets_general;
				this.form.assets = this.form.assets_general;
				this.form.assets_general = [];
				if (
					typeof this.form.appraise_has_assets !== "undefined" &&
					this.form.appraise_has_assets.length > 0
				) {
					this.form.appraise_has_assets.forEach(asset => {
						this.form.assets_general.push({
							assets_general_id: asset.assets_general_id,
							asset_property_detail_id: asset.asset_property_detail_id,
							version: asset.version
						});
					});
				}
			}
		},
		async getProfiles() {
			const profile = this.$store.getters.profile;
			this.checkRole =
				(this.createdBy && profile.data.user.id === this.createdBy.id) ||
				["ROOT_ADMIN", "SUB_ADMIN"].includes(profile.data.user.roles[0].name);
		},

		async handleCancel() {
			if (this.$route.name === "certificate.create") {
				return this.$router.push({ name: "certificate.index" });
			} else if (this.$route.name === "certificate.edit") {
				this.$router.go(-1);
			}
		},
		getAddressEdit() {
			const province = this.form.step_1.province;
			const district = this.form.step_1.district;
			const ward = this.form.step_1.ward;
			const street = this.form.step_1.street;
			const distance = this.form.step_1.distance;
			if (province) {
				this.provinceName = province.name;
				this.addressName.province = province.name;
			} else {
				this.provinceName = null;
				this.addressName.province = null;
			}
			if (district) {
				this.districtName = district.name;
				this.addressName.district = district.name;
			} else {
				this.districtName = null;
				this.addressName.district = null;
			}
			if (
				this.districtName &&
				(this.districtName.toLowerCase() === "thành phố biên hòa" ||
					this.districtName.toLowerCase() === "thành phố long khánh")
			) {
				this.radius = 1;
				this.distance = 1000;
			} else {
				this.radius = 2;
				this.distance = 2000;
			}
			if (ward) {
				this.wardName = ward.name;
				this.addressName.ward = ward.name;
			} else {
				this.wardName = null;
				this.addressName.ward = null;
			}
			if (street) {
				this.streetName = street.name;
				this.addressName.street = street.name;
			} else {
				this.streetName = null;
				this.addressName.street = null;
			}
			if (distance) {
				this.addressName.distance = distance.name;
			} else {
				this.addressName.distance = null;
			}
			this.getFullAddress();
		},
		getProvinces() {
			if (this.form.step_1.province_id) {
				this.getDistrictsByProvinceId(this.form.step_1.province_id);
				if (
					this.$route.name === "certification_asset.apartment.edit" ||
					("id" in this.$route.params &&
						this.$route.name === "certification_asset.apartment.create")
				) {
					this.getAddressEdit();
				}
			}
		},
		async getDistrictsByProvinceId(id) {
			await WareHouse.getDistrictsByProvinceId(id)
				.then(resp => {
					this.districts = resp.data;
					if (this.form.step_1.district_id) {
						this.getProjectsByDistrictId(this.form.step_1.district_id);
						this.getWardsByDistrictId(this.form.step_1.district_id);
						this.getStreetByDistrictId(this.form.step_1.district_id);
					}
				})
				.catch(err => {
					this.isSubmit = false;
					throw err;
				});
		},
		async getProjectsByDistrictId(id) {
			await WareHouse.getProjectsByDistrictId(id)
				.then(resp => {
					this.projects = resp.data;
				})
				.catch(err => {
					throw err;
				})
				.finally(() => {
					this.getProjects();
				});
		},
		getWardsByDistrictId(id) {
			let wards = this.districts.filter(item => item.id === id);
			this.wards = wards[0].wards;
			this.wards.forEach(item => {
				item.name = this.formatCapitalize(item.name);
			});
		},
		getStreetByDistrictId(id) {
			let streets = this.districts.filter(item => item.id === id);
			this.streets = streets[0].streets;
			this.streets.forEach(item => {
				item.name = this.formatCapitalize(item.name);
			});
			if (
				this.form.step_1.street_id !== "" &&
				this.form.step_1.street_id !== undefined &&
				this.form.step_1.street_id !== null
			) {
				this.getDistanceByStreetId(this.form.step_1.street_id);
			}
		},
		async getDistanceByStreetId(id) {
			let distances = this.streets.filter(item => item.id === id);
			this.distances = distances[0].distances;
		},
		findProvince() {
			const province = this.provinces.find(
				province => province.id === this.form.step_1.province_id
			);
			if (province) {
				this.provinceName = province.name;
				this.addressName.province = province.name;
			} else {
				this.provinceName = null;
				this.addressName.province = null;
			}
			this.findDistrict();
			this.getFullAddress();
		},
		findDistrict() {
			const district = this.districts.find(
				district => district.id === this.form.step_1.district_id
			);
			if (district) {
				this.districtName = district.name;
				this.addressName.district = district.name;
			} else {
				this.districtName = null;
				this.addressName.district = null;
			}
			if (
				this.districtName &&
				(this.districtName.toLowerCase() === "thành phố biên hòa" ||
					this.districtName.toLowerCase() === "thành phố long khánh")
			) {
				this.radius = 1;
				this.distance = 1000;
			} else {
				this.radius = 2;
				this.distance = 2000;
			}
			this.findWard();
			this.findStreet();
			this.getFullAddress();
		},
		findWard() {
			const ward = this.wards.find(
				ward => ward.id === this.form.step_1.ward_id
			);
			if (ward) {
				this.wardName = ward.name;
				this.addressName.ward = ward.name;
			} else {
				this.wardName = null;
				this.addressName.ward = null;
			}
			this.getFullAddress();
		},
		findStreet() {
			const street = this.streets.find(
				street => street.id === this.form.step_1.street_id
			);
			if (street) {
				this.streetName = street.name;
				this.addressName.street = street.name;
			} else {
				this.streetName = null;
				this.addressName.street = null;
			}
			this.findDistance();
			this.getFullAddress();
		},
		findDistance() {
			const distance = this.distances.find(
				distances => distances.id === this.form.step_1.distance_id
			);
			if (distance) {
				this.addressName.distance = distance.name;
			} else {
				this.addressName.distance = null;
			}
		},

		// change location
		changeProvince(id) {
			this.form.step_1.district_id = "";
			this.form.step_1.ward_id = "";
			this.form.step_1.street_id = "";
			this.form.step_1.distance_id = "";
			this.districts = [];
			this.wards = [];
			this.streets = [];
			this.distances = [];
			if (this.form.step_1.province_id !== 0) {
				this.getDistrictsByProvinceId(id);
			}
			this.findProvince();
			this.getFullAddress();
		},

		changeDistrict(id) {
			this.wards = [];
			this.streets = [];
			this.distances = [];
			this.form.step_1.ward_id = "";
			this.form.step_1.street_id = "";
			this.form.step_1.distance_id = "";
			if (this.form.step_1.district_id) {
				this.getWardsByDistrictId(id);
				this.getStreetByDistrictId(id);
			}
			this.findDistrict();
		},
		changeWard() {
			this.findWard();
		},
		changeStreet(id) {
			this.distances = [];
			this.form.step_1.distance_id = "";
			if (this.form.step_1.street_id) {
				this.getDistanceByStreetId(id);
			}
			this.findStreet();
		},
		changeDistance() {
			this.findDistance();
		},
		getFullAddress() {
			this.full_address =
				`${this.wardName ? this.wardName + ", " : ""}` +
				`${this.districtName ? this.districtName + ", " : ""}` +
				`${
					this.provinceName
						? this.provinceName.includes("Thành phố")
							? this.provinceName
							: "tỉnh " + this.provinceName.trim()
						: ""
				}`;
			this.full_address_street =
				`${this.streetName ? this.streetName + ", " : ""}` +
				`${this.wardName ? this.wardName + ", " : ""}` +
				`${this.districtName ? this.districtName + ", " : ""}` +
				`${this.provinceName ? this.provinceName : ""}`;
			if (this.$route.name === "certification_asset.apartment.create") {
				this.getInfo();
			}
		},
		changeAssetType(id) {
			const assetType = this.propertyTypes.find(
				assetType => assetType.id === this.form.step_1.asset_type_id
			);
			if (assetType) {
				this.assetName = assetType.description;
			} else {
				this.assetName = null;
			}
			if (id === 38) {
				this.isHaveContruction = true;
			} else {
				this.isHaveContruction = false;
			}
			this.getInfo();
		},
		getInfo() {
			if (this.assetName === "Đất trống" && this.full_address) {
				this.form.step_1.appraise_asset = `Quyền sử dụng đất tại ${this.full_address}`;
			} else if (this.assetName === "Đất có nhà" && this.full_address) {
				this.form.step_1.appraise_asset = `Quyền sử dụng đất và CTXD tại ${this.full_address}`;
			}
		},
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		formatCapitalize(word) {
			return word.toLowerCase().replace(/(?:^|\s|[-"'([{])+\S/g, function(x) {
				return x.toUpperCase();
			});
		},
		async getDictionary() {
			await WareHouse.getDictionaries()
				.then(resp => {
					this.propertyTypes = resp.data.loai_tai_san.filter(
						item => item.acronym === "CC"
					);
					this.landType = resp.data.loai_dat;
					this.topographic = resp.data.dia_hinh;
					this.landShapes = resp.data.hinh_dang_dat;
					this.socialSecurities = resp.data.an_ninh_moi_truong_song;
					let basic_utilities = resp.data.tien_ich_co_ban;
					this.basic_utilities = basic_utilities.filter(
						item => item.acronym !== null
					);
					this.directions = resp.data.huong_can_ho;
					this.businesses = resp.data.kinh_doanh;
					this.paymentMethods = resp.data.dieu_kien_thanh_toan;
					this.conditions = resp.data.dieu_kien_ha_tang;
					this.fengshuies = resp.data.phong_thuy;
					this.zones = resp.data.quy_hoach_hien_trang;
					this.materials = resp.data.giao_thong_chat_lieu;
					this.roughes = resp.data.giao_thong;
					this.points = resp.data.vi_tri_dat;
					this.imageDescriptions = resp.data.mo_ta_hinh_anh;
					// data for step 3
					this.buildingCategories = resp.data.cap_nha;
					this.housingTypes = resp.data.loai_nha;
					this.buildingRates = resp.data.hang_nha;
					this.buildingStructure = resp.data.cau_truc_biet_thu;
					this.buildingAperture = resp.data.khau_do;
					this.buildingFactoryType = resp.data.loai_nha_may;
					this.housingTypes.forEach(item => {
						item.description = this.formatSentenceCase(item.description);
					});
					this.directions.forEach(item => {
						item.description = this.formatSentenceCase(item.description);
					});
					this.materials.forEach(item => {
						item.description = this.formatSentenceCase(item.description);
					});
					this.landShapes.forEach(item => {
						item.description = this.formatSentenceCase(item.description);
					});
					this.propertyTypes.forEach(item => {
						item.description = this.formatSentenceCase(item.description);
					});
					// const res = await WareHouse.getInterior()
					this.furniture_list = resp.data.chat_luong_noi_that;
					this.loai_can_ho = resp.data.loai_can_ho;
					this.furniture_list.forEach(item => {
						item.description = this.formatSentenceCase(item.description);
					});
					this.key_step_1 += 1;
				})
				.catch(err => {
					this.isSubmit = false;
					throw err;
				});
		},
		async getAppraiseLaws() {
			await Certificate.getAppraiseLaws().then(resp => {
				if (resp.data && resp.data.phap_ly) {
					this.juridicals = resp.data.phap_ly;
					this.juridicals.push({
						content: "Văn bản pháp lý khác",
						created_at: new Date(),
						date: "",
						deleted_at: null,
						document_type: "",
						id: 0,
						is_defaults: false,
						provinces: "Tất cả",
						type: "PHAP_LY"
					});
				} else {
					this.juridicals = [];
				}
			});
		},
		async getAppraiseOthers() {
			await Certificate.getAppraiseOthers().then(resp => {
				this.appraisalPurposes = resp.data.muc_dich_tham_dinh_gia.filter(item =>
					item.dictionary_acronym.includes("BDS")
				);
				this.appraisalFacility = resp.data.co_so_tham_dinh.filter(item =>
					item.dictionary_acronym.includes("BDS")
				);
				this.appraisalPrinciples = resp.data.nguyen_tac_tham_dinh.filter(item =>
					item.dictionary_acronym.includes("BDS")
				);
				this.approach = resp.data.cach_tiep_can_chi_phi.filter(item =>
					item.dictionary_acronym.includes("BDS")
				);
				this.methodsUsed = resp.data.phuong_phap_tham_dinh_su_dung.filter(
					item => item.dictionary_acronym.includes("BDS")
				);

				this.unifyIndicativePrice = resp.data.thong_nhat_muc_gia_chi_dan;
				this.compositeLandRemaning = resp.data.tinh_gia_dat_hon_hop_con_lai;
				this.planningViolationPrice = resp.data.tinh_gia_dat_vi_pham_quy_hoach;
				this.planningViolationPrice = resp.data.tinh_gia_dat_vi_pham_quy_hoach;
			});
		},
		findAppraisalFacility() {
			this.form.step_3.value_base_and_approach.basis_property_id = this.appraisalFacility.find(
				appraisalFacility => appraisalFacility.is_defaults === true
			).id;
		},
		findAppraisalPrinciples() {
			this.form.step_3.value_base_and_approach.principle_id = this.appraisalPrinciples.find(
				appraisalPrinciple => appraisalPrinciple.is_defaults === true
			).id;
		},
		findApproach() {
			this.form.step_3.value_base_and_approach.approach_id = this.approach.find(
				approach => approach.is_defaults === true
			).id;
		},
		findMethodsUsed() {
			this.form.step_3.value_base_and_approach.method_used_id = this.methodsUsed.find(
				methods => methods.is_defaults === true
			).id;
		},
		getProjects() {
			if (this.form.step_1.project_id) {
				this.getBlocks(this.form.step_1.project_id);
			}
		},
		getBlocks(id) {
			let project = this.projects.filter(item => item.id === id);
			this.blocks = project[0].block;
			if (this.form.step_1.apartment_asset_properties.block_id) {
				this.getFloors(this.form.step_1.apartment_asset_properties.block_id);
			}
		},
		getFloors(id) {
			let block = this.blocks.filter(item => item.id === id);
			this.floors = block[0].floor;
			// if (this.form.step_1.apartment_asset_properties.floor_id) {
			// 	this.getApartments(this.form.step_1.apartment_asset_properties.floor_id)
			// }
		},
		async getApartments(id) {
			const res = await WareHouse.getApartmentFloor(id);
			if (res.data) {
				this.apartments = [...res.data];
			}
		},
		async handleChangeProject(projectId) {
			this.blocks = [];
			this.floors = [];
			this.apartments = [];
			this.form.step_1.apartment_asset_properties.block_id = "";
			this.form.step_1.apartment_asset_properties.floor_id = "";
			this.form.step_1.apartment_asset_properties.apartment_id = "";
			if (projectId) {
				let project = this.projects.filter(item => item.id === projectId);
				this.form.step_1.coordinates = project[0].coordinates;
				if (project[0].utilities) {
					this.form.step_1.apartment_asset_properties.utilities =
						project[0].utilities;
				}
				this.form.step_1.province_id = project[0].province_id;
				this.form.step_1.district_id = project[0].district_id;
				this.form.step_1.ward_id = project[0].ward_id;
				this.form.step_1.street_id = project[0].street_id;
				this.getProvinces();
				let provinceName = "";
				let districtName = "";
				let wardName = "";
				let streetName = "";
				if (project[0].province) {
					provinceName = project[0].province.name;
				}
				if (project[0].district) {
					districtName = project[0].district.name;
				}
				if (project[0].ward) {
					wardName = project[0].ward.name;
				}
				if (project[0].street) {
					streetName = this.formatCapitalize(project[0].street.name);
				}
				this.form.step_1.appraise_asset =
					`${streetName}, ${wardName}, ` + `${districtName}, ` + provinceName;
				this.getBlocks(+projectId);
				this.key_step_1 += 1;
			}
		},
		handleChangeBlock(blockId) {
			this.floors = [];
			this.apartments = [];
			this.form.floor_id = "";
			this.form.apartment_id = "";
			if (blockId) {
				let block = this.blocks.filter(item => item.id === blockId);
				this.form.step_1.apartment_asset_properties.handover_year =
					block[0].handover_year;
				this.getFloors(blockId);
			}
		},
		handleChangeFloor(floorId) {
			this.apartments = [];
			this.form.apartment_id = "";
			if (floorId) {
				this.getApartment(floorId);
			}
		},
		changeAparment(event) {
			this.form.apartment_id = event;
		},
		getEditAsset(certificate) {
			let status = certificate.status;
			let sub_status = certificate.sub_status;
			let config = this.principleConfig.find(
				i => i.status === status && i.sub_status === sub_status
			);
			return config && config.edit.asset ? config.edit.asset : false;
		},
		async getHistoryTimeline(id) {
			const res = await CertificateAsset.getHistoryTimelineApartment(id);
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
		showHistoryDrawerDrawer() {
			this.visibleHistoryDrawer = true;
			this.getHistoryTimeline(this.idData);
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
		async handleSaveAdditional() {
			this.isSubmit = true;
			let id = this.idData ? this.idData : "";
			const res = await CertificateAsset.submitAdditionalAsset(
				this.real_estate,
				id
			);
			if (res.data) {
				this.$toast.open({
					message: "Lưu bổ sung thông tin thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
			this.isSubmit = false;
			this.visibleAdditionalDrawer = false;
		},
		formatDateTime(value) {
			return moment(String(value)).format("HH:mm DD/MM/YYYY");
		},
		handlePrint() {
			this.printEstimateAssetPrice();
		},
		handleCancelProperty() {
			this.openCancelAppraisal = true;
			this.message = "Bạn có muốn hủy tài sản thẩm định này không ?";
		},

		// function hủy tài sản
		async handleActionCancelAppraise() {
			let status = 5;
			const res = await AppraiseData.updateStatusRealestate(
				this.idData,
				status
			);
			if (res.data && res.data.status === 5) {
				await this.$toast.open({
					message: "Hủy tài sản" + this.idData + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				this.openNotification = await false;
				await this.$router
					.push({ name: "certification_asset.index" })
					.catch(_ => {});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},
		async printEstimateAssetPrice() {
			let id = this.idData ? this.idData : "";
			const res = await CertificateAsset.postEstimateAssetPriceApartment(id);
			if (res.data) {
				this.reportData = res.data;
				this.openPrint = true;
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			}
		}
	},
	async beforeMount() {
		this.getDictionary();
		this.getAppraiseLaws();
		this.getAppraiseOthers();
	},
	mounted() {},
	computed: {
		isEditStatus() {
			let check = false;
			const certificate = this.certificate;
			if (this.checkRole) {
				switch (this.status) {
					case 1:
						check = true;
						break;
					case 2:
						if (certificate) {
							check = this.getEditAsset(certificate);
						} else {
							check = true;
						}
						break;
					case 3:
						if (certificate) {
							check = this.getEditAsset(certificate);
						}
						break;
				}
			}
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

/deep/ .dropup.dropdown-menu {
	background: transparent !important;
	box-shadow: none !important;
}
.certification-asset {
	// padding-left: 16px;
	// padding-right: 16px;
	.step7 {
		/deep/ .wizard-tab-content {
			padding: 5px 5px 40px 0px !important;
		}
	}
	.stepTitle {
		font-size: 18px !important;
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
.btn_loading {
	position: relative;
	color: white !important;
	text-shadow: none !important;
	pointer-events: none;
}
.btn_loading:after {
	content: "";
	display: inline-block;
	vertical-align: text-bottom;
	border: 1px solid wheat;
	border-right-color: transparent;
	border-radius: 50%;
	color: #ffffff;
	position: absolute;
	width: 1rem;
	height: 1rem;
	left: calc(50% - 0.5rem);
	top: calc(50% - 0.5rem);
	-webkit-animation: spinner-border 0.75s linear infinite;
	animation: spinner-border 0.75s linear infinite;
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
/deep/ .ant-timeline-item-content {
	margin-left: 25px;
	p {
		margin-bottom: 0.2em;
	}
}
/deep/ .ant-timeline-item-tail {
	border-left: 2px solid #26bf5fad;
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
