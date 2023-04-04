<template>
  <div
    class="modal-detail d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="font-weight-bold">Chọn avatar</h3>
        <div class="btn--contain">
          <div class="btn--cancel" @click="handleCancel">
            <img src="../../assets/icons/ic_cancel-1.svg" alt="cancel">
          </div>
        </div>
      </div>
      <div class="card-body" v-if="picList.length > 0">
        <button class="btn btn-change btn-change--prev" :disabled="activeImage === 0" @click="onClickChangeImage('prev')">
          <img src="../../assets/icons/prev.svg" alt="">
        </button>
        <button class="btn btn-change btn-change--next" :disabled="activeImage === picList.length - 1" @click="onClickChangeImage('next')">
          <img src="../../assets/icons/next.svg" alt="">
        </button>
        <div class=" d-flex justify-content-center">
          <div class="image-container">
            <img :src="`${picList[activeImage].link}`" alt="">
          </div>
        </div>
      </div>
      <div class="card-body" v-if="picList.length === 0">
        <h3 class="py-5">Không có hình ảnh để hiển thị</h3>
      </div>
      <div class="card-footer" v-if="picList.length > 0">
        <div class="image-list-container" v-if="picList">
          <div v-for="(item, index) in picList" v-bind:key="index"
               :class="activeImage === index ? 'image-control active' : 'image-control'"
               @click="onChangeImageIndex(index)">
            <img :src="`${item.link}`" alt="">
          </div>
        </div>
      </div>
      <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-orange" @click="chooseAvatar()">
          Chọn
        </button>
      </div>
    </div>
  </div>
</template>

<script>

export default {
	name: 'ModalAvatarCustomer',
	props: ['pics'],
	data () {
		return {
			picList: this.pics,
			image: '',
			activeImage: 0,
			activeName: 0
		}
	},
	methods: {
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		onClickChangeImage (action) {
			switch (action) {
			case 'prev':
				this.activeImage = this.activeImage - 1
				break
			case 'next':
				this.activeImage = this.activeImage + 1
				break
			}
		},
		onChangeImageIndex (index) {
			this.activeImage = index
			this.activeName = index
		},
		chooseAvatar (event) {
			this.$emit('choose-image', this.picList[this.activeImage].link)
			this.$emit('cancel', event)
		}
	},
	mounted () {
		this.image = process.env.API_URL
	}
}
</script>

<style lang="scss" scoped>

.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, .6);
  padding: 20px;

  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1100px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    @media (max-width: 787px) {
      padding: 20px 10px;
    }

    &-header {
      display: flex;
      justify-content: flex-end;

      h3 {
        color: #333333;
      }

      img {
        cursor: pointer;
      }
    }

    &-body {
      text-align: center;
      position: relative;

      p {
        color: #333333;
        margin-bottom: 40px;
      }

      .btn__group {
        .btn {
          max-width: 150px;
          width: 100%;
          margin: 0 10px;
        }
      }
    }
  }
}

.content-detail {
  overflow-y: auto;
  overflow-x: hidden;
}

.image-list-container {
  overflow: auto;
  text-align: center;
  display: flex;

  .image-control {
    max-width: 100px;
    min-width: 100px;
    max-height: 100px;
    min-height: 100px;
    margin: 10px 5px;
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
  width: 500px;
  height: 500px;
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
</style>
