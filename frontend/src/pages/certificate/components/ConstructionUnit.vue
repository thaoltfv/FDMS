<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Đơn vị xây dựng</h3>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <div class="container-content__detail row align-items-center mb-0">
        <div class="container-content__title col-3 mb-0">Chọn đơn vị xây dựng</div>
        <div class="col d-flex align-items-center">
          <button type="button" class="btn btn-orange" @click="handleOpenModalExpertise">Chỉnh sửa</button>
          <div class="text-danger">
            Đã chọn {{construction_company.length}} đơn vị
          </div>
        </div>
      </div>
    </div>
    <ModalConstructionUnit
      v-if="openModalDocuments"
      @cancel="cancelData"
      :documents="documents"
      :data="data"
      :title="title"
      @action="saveData"
    />
  </div>
</template>

<script>

import ModalConstructionUnit from './modals/ModalConstructionUnit'
export default {
	name: 'ConstructionUnit',
	props: ['constructions', 'construction_company'],
	data () {
		return {
			showCard: true,
			title: '',
			documents: [],
			data: [],
			openModalDocuments: false
		}
	},
	components: {
		ModalConstructionUnit
	},
	methods: {
		saveData (event) {
			this.$emit('construction', event)
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		cancelData () {
			this.openModalDocuments = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		handleOpenModalExpertise () {
			this.title = 'Đơn giá xây dựng'
			this.documents = this.constructions
			this.data = this.construction_company
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		}
	}
}
</script>

<style scoped lang="scss">
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 768px) {
      margin-bottom: 20px;
    }
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      @media (max-width: 768px) {
        padding: 12px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .contain-table {
    &__tangible {
      overflow-y: hidden;
      overflow-x: auto;
    }
    @media (max-width: 1440px) {
      overflow-y: hidden;
      overflow-x: auto;
    }
    .table-property{
      width: 100%;
    }
  }
  .table-property{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 500;
        @media (max-width: 787px) {
          padding: 12px;
        }
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
          border-right: none;
        }
        padding: 20px 14px;
      }
    }
    &__order {
      tbody{
        td{
          &:first-child{
            width: 40%;
          }
          &:last-child{
            width: 70px;
          }
          padding: 20px 70px;
          @media (max-width: 1023px) {
            padding: 20px 30px;
          }
        }
      }
    }
  }
  .contain-total{
    display: grid !important;
    margin-right: 0;
    grid-template-columns: 1fr 1fr;
    color: #333333;
    @media (max-width: 1440px) {
      display: block !important;
    }
    .num{
      padding: 0 11px 0 24px;
      height: 35px;
      line-height: 1.5;
      width: 180px;
      border-radius: 5px;
      border: 1px solid #555555;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      background: #f1f1f1 !important;
      cursor: not-allowed;
      user-select: none;
      @media (max-width: 787px) {
        width: 100% !important;
      }
      p{
        margin-bottom: 0;
      }
      &-id{
        color: #FAA831;
        text-align: center !important;
        background: #FFFFFF !important;
        border: none;
        width: 100%;
        padding: 0;
      }
    }
    .name{
      margin-bottom: 0;
      font-size: 1.125rem;
      font-weight: 500;
      margin-right: 20px;
      @media (max-width: 1440px) {
        margin-bottom: 10px;
        font-weight: 700;
      }
    }
    &__last{
      .num{
        width: 315px;
        @media (max-width: 767px){
          width: calc(100vw - 120px) ;
        }
      }
    }
    &__table{
      grid-template-columns: 1fr;
      .num{
        text-align: left;
        margin: auto;
        &-id{
          cursor: pointer;
          color: #FAA831;
          text-align: center !important;
        }
      }
    }
  }
  .coordinate{
    color: #000000;
    background: #f1f1f1;
    padding: 0 11px 0 24px;
    display: flex;
    align-items: center;
    height: 35px;
    border-radius: 5px;
    border: 1px solid #555555;
    .num{
      p{
        margin-bottom: 0;
      }
    }
  }
  .btn{
    &-white{
      max-height: none;

      line-height: 19.07px;
      min-width: 153px;
      margin-right: 15px;
      &:last-child{
        margin-right: 0;
      }
    }
    &-orange {
      max-height: none;

      line-height: 19.07px;
      min-width: 153px;
      margin-right: 15px;
      color: #FFFFFF;
      background: #FAA831;
    }
    &-contain{
      margin-bottom: 55px;
      @media (max-width: 768px) {
        margin-bottom: 30px;
      }
    }
  }
  .btn-delete{
    display: flex;
    align-items: center;
    cursor: pointer;
    background: #FFFFFF;
    border: 0.777778px solid #000000;
    border-radius: 5.88235px;
    padding: 10px;
    margin: auto;
    width: 36px;
    height: 36px;
    img{
      width: 100%;
      height: auto;
    }
  }
  .btn-property{
    padding: 10px;
  }
  .img-dropdown {
    cursor: pointer;
    width: 18px;
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
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
</style>
