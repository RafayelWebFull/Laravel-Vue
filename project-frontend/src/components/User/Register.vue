<template>
  <div>
    <b-container>
      <b-row>
        <b-col class="">
          <h2>Sign up</h2>
          <div v-if="validationErrors">
            <ul class="alert alert-danger">
              <li v-for="(value, key, index) in validationErrors">{{ value }}</li>
            </ul>
          </div>
          <b-form @submit.prevent="onSubmit" @reset="onReset" v-if="show" enctype="multipart/form-data">
            <b-form-group id="input-group-1" label="Name:"  label-for="input-1">
              <b-form-input
                id="input-1"
                v-model="form.name"
                required
                type='text'
                placeholder="Enter name"
              ></b-form-input>
            </b-form-group>
            <b-form-group id="input-group-2" label="Email:" label-for="input-2">
              <b-form-input
                id="input-3"
                v-model="form.email"
                type="email"
                required
                placeholder="Enter email"
              ></b-form-input>
            </b-form-group>
            <b-form-group v-if="this.showNick === true" id="input-group-3" label="NickName:" label-for="input-3">
              <b-form-input
                id="input-2"
                v-model="form.nickname"
                required
                placeholder="Enter Nickname "
              ></b-form-input>
            </b-form-group>
            <b-form-group
              id="input-group-8"
              label="Choose Avatar"
              label-for="input-8">
             <b-file v-model="form.avatar" accept="image/x-png,image/gif,image/jpeg"></b-file>
            </b-form-group>
            <b-form-group id="input-group-4" label="Your Password:" label-for="input-4">
              <b-form-input
                id="input-4"
                v-model="form.password"
                required
                type='password'
                placeholder="Enter you Password"
              ></b-form-input>
            </b-form-group>
            <b-form-group id="input-group-5" label="Confirm your password:" label-for="input-5">
              <b-form-input
                id="input-5"
                v-model="form.password_confirmation"
                required
                type='password'
                placeholder="Confirm Password"
              ></b-form-input>
            </b-form-group>
            <b-form-group id="input-group-6" label="Please Choose Your Role:" label-for="input-6">
              <b-form-select @change="onChange()  "
                id="input-6"
                v-model="form.role"
                required
                type="select"
                placeholder="password"
              >
                <option value="" disabled>Choose Your Role</option>
                <option value="user">User</option>
                <option value="blogger">Blogger</option>
              </b-form-select>
            </b-form-group>
          <b-button type="submit" variant="primary" class='btn'>Sign up</b-button>
          </b-form>
<!--          <router-link :to="{name: 'Login'}" class="btn btn-success ml-2">-->
<!--            Already have an Account?-->
<!--          </router-link>-->
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import axios from 'axios'
import router from "../../router";
export default {
  data() {
    return {
      form: {
        name: "6451651",
        email: "sdfsbf@gmail.com",
        password: "password",
        avatar: null,
        password_confirmation: "password",
        nickname: "",
        role: '',
      },
      show: true,
      showNick : false,
      errors: ["email","password","name"]
    }
  },
  mounted() {
    if(this.$store.getters.authUser){
      router.push("/")
    }
  },
  computed:{
    massages(){
     return this.$store.getters.RegisterErrors
    },
    validationErrors(){
      if(this.$store.getters.error){
        let errors = Object.values(this.$store.getters.error);
        errors = errors.flat();
        return errors;
      }else{
        return null;
      }
    }
  },
  methods: {
    onSubmit(evt) {
      let formData = new FormData()
      formData.append('file', this.form.avatar)
      formData.append('name', this.form.name)
      formData.append('email', this.form.email)
      formData.append('password', this.form.password)
      formData.append('password_confirmation', this.form.password_confirmation)
      formData.append('nickname', this.form.nickname)
      formData.append('role', this.form.role)
      this.$store.dispatch("register",formData)

    },
    onReset(evt) {
      evt.preventDefault()
      this.form.name = ''
      this.form.email = ''
      this.form.password = ''
      this.form.password_confirm = ''
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    },
    onChange(e){
      if(this.form.role == "blogger"){
        this.showNick = true
      }else{
        this.showNick = false
      }
    }

  }
}
</script>

<style scoped>
.btn {
  float:right;
}
p {
  padding-top: 30px;
}
</style>
