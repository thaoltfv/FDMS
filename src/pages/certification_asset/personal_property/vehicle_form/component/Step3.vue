<template>
  <div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Cơ sở thẩm định</h3>
          <img class="img-dropdown" :class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailAppraise = !showCardDetailAppraise">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailAppraise">
        <div class="container-fluid color_content">

          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Cơ sở giá trị của tài sản thẩm định giá</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4 align-items-center" v-for="(itemFacility, indexFacility) in appraisalFacility" :key="indexFacility">
                <div class="d-flex form-group-container">
                  <input type="radio" :name="itemFacility.type" :id="itemFacility.id" :value="itemFacility.id" v-model="data.basis_property_id">
                  <div style="margin-left: 0.5rem" class=""><label style="margin-bottom: unset !important" :for="itemFacility.id">{{itemFacility.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Nguyên tắc thẩm định</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4 align-items-center" v-for="(itemPrinciple, indexPrinciple) in appraisalPrinciples" :key="indexPrinciple">
                <div class="d-flex form-group-container">
                  <input type="radio" :name="itemPrinciple.type" :id="itemPrinciple.id" :value="itemPrinciple.id" v-model="data.principle_id">
                  <div style="margin-left: 0.5rem" class=""><label style="margin-bottom: unset !important" :for="itemPrinciple.id">{{itemPrinciple.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Cách tiếp cận</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4 align-items-center" v-for="(itemApproach, indexApproach) in approach" :key="indexApproach">
                <div class="d-flex form-group-container">
                  <input type="radio" :name="itemApproach.type" :id="itemApproach.id" :value="itemApproach.id" v-model="data.approach_id">
                  <div style="margin-left: 0.5rem" class=""><label style="margin-bottom: unset !important" :for="itemApproach.id">{{itemApproach.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Phương pháp sử dụng</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4 align-items-center" v-for="(itemUsedMethod, indexUsedMethod) in methodsUsed" :key="indexUsedMethod">
                <div class="d-flex form-group-container">
                    <input type="radio" :name="itemUsedMethod.type" :id="itemUsedMethod.id" :value="itemUsedMethod.id" v-model="data.method_used_id">
                  <div style="margin-left: 0.5rem" class=""><label style="margin-bottom: unset !important" :for="itemUsedMethod.id">{{itemUsedMethod.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2 ">
            <div class="d-flex col-12 col-lg-4 color_content form-label font-weight-bold align-items-center form-group-container">Giả thiết và giả thiết đặc biệt</div>
              <div class="col-12 col-lg-8 align-items-center ">
                <InputTextarea
                  nonLabel="Giả thiết và giả thiết đặc biệt"
                  v-model="data.document_description"
                  rules="required"
                  vid="document_description"
                  :rows="3"
                  :maxLength="1000000"
                  :autosize="true"
                  class="col-12"
                />
              </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'

export default {
	components: {
		InputText,
		InputTextarea
	},
	props: ['data', 'appraisalPurposes', 'appraisalFacility', 'appraisalPrinciples', 'approach', 'methodsUsed'],
	data () {
		return {
			form: {
				name: ''
			},
			showCardDetailAppraise: true
		}
	}
}
</script>
<style lang="scss" scoped>
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6 !important;
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
.container_content {
  border-bottom: 1px solid #E8E8E8;
}
</style>
