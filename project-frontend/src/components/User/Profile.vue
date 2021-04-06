<template>
  <div>
    <b-navbar toggleable="lg" type="dark" variant="secondary">
      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-navbar-brand href="/">Home</b-navbar-brand>
        </b-navbar-nav>
        <b-navbar-nav class="ml-auto mr-5">
          <h4 class="text-dark" > {{ user.name}}</h4>
          <b-nav-item-dropdown class="text-dark"  right>
            <!-- Using 'button-content' slot -->
            <b-dropdown-item href=""><router-link :to="{name: 'Profile'}">Profile</router-link></b-dropdown-item>
            <b-dropdown-item href="#" @click="SignOut">Sign Out</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
    <div class="container mt-5">
    <div class="d-flex">
    <div class="card-body">
      <div class="d-flex flex-column align-items-center text-center">
        <img :src="`http://task.loc/storage/avatar/${user.avatar}`" alt="Admin" class="rounded-circle" width="150">
        <div class="mt-3"><h4>John Doe</h4><p class="text-secondary mb-1">Full Stack Developer</p>
          <p class="text-muted font-size-sm">Bay Area, San Francisco, CA</p>
        </div>
      </div>
    </div>
    <div  v-if="this.showInfo === true" class="card-body">
      <div class="row">
        <div class="col-sm-3">
        <h6 class="mb-0"> Name</h6>
        </div><div class="col-sm-9 text-secondary"> {{user.name}}</div>
      </div><hr>
      <div class="row">
        <div class="col-sm-3"><h6 class="mb-0">Email</h6>
      </div>
        <div class="col-sm-9 text-secondary"> {{user.email}}</div>
      </div><hr>
      <div class="row">
        <div class="col-sm-3">
          <h6 class="mb-0">Role</h6>
        </div><div class="col-sm-9 text-secondary">{{user.role}}</div>
      </div><hr>
      <div class="row">
        <div class="col-sm-3">
          <h6 class="mb-0">Created Time</h6>
        </div>
        <div class="col-sm-9 text-secondary">{{ user.created_at}}</div>
      </div><hr>
      <b-button v-b-modal.modal-profile-change variant="info">Edit</b-button>
      <b-button v-b-modal.modal-password-change variant="danger">Change Password</b-button>
    </div>
    <div>
      <b-modal  id="modal-profile-change" hide-footer
               ref="modal"
               title="Submit Your Name"
                class="modal">
        <template #modal-title>
         Edit <code>Profile</code>
        </template>
      <b-form @submit="onSubmitProfile" @reset="onReset" v-if="show">
        <div v-if="validationErrors">
          <ul class="alert alert-danger">
            <li v-for="(value, key, index) in validationErrors">{{ value }}</li>
          </ul>
        </div>
        <b-form-group id="input-group-1" label="Your Name:" label-for="input-5">
          <b-input
            id="input-5"
            required
            type='text'
            :value="user.name"
            v-model="newName"
            placeholder="User Name"
          ></b-input>
        </b-form-group>
        <b-form-group v-if="user.nickname" id="input-group-8" label="Your Name:" label-for="input-5">
          <b-input
            id="input-5"
            required
            type='text'
            v-model="user.nickname"
            placeholder="Confirm Password"
          ></b-input>
        </b-form-group>
        <b-form-group
          id="input-group-4"
          label="Choose Avatar"
          label-for="input-1"
          description="">
        <b-file type="file" @change="onFile"  accept="image/x-png,image/gif,image/jpeg" placeholder="Default.png"></b-file>
        </b-form-group>
        </b-form>
        <b-button  class="mt-3" variant="outline-danger" block @click="hideModal">Close Me</b-button>
        <b-button type="submit" class="mt-3" variant="outline-info" block @click="onSubmitProfile">Save</b-button>
      </b-modal>
      <b-modal id="modal-password-change" hide-footer
               ref="modal"
               title="Submit Your Name"
               class="modal">
        <template #modal-title>
          Change <code>Password</code>
         <h6 class="text-danger mt-2">{{errorMassage}}</h6>
        </template>
        <b-form  @submit="onSubmit" @reset="onReset" v-if="show">
          <b-form-group id="input-group-3" label="Password:" label-for="input-5">
            <b-input
              id="input-4"
              required
              type='password'
              v-model="form.password"
              placeholder="Password"
            ></b-input>
          </b-form-group>
            <b-button type="button" v-if="this.passwordTrue != true" class="mb-2" @click="CheckPassword">Check Password</b-button>
          <b-form-group v-if="this.passwordTrue" id="input-group-2" label="New Password:" label-for="input-5">
            <b-input
              id="input-5"
              required
              type='password'
              v-model="NewPasswords.newPassword"
              placeholder="New Password"
            ></b-input>
          </b-form-group>
          <b-form-group v-if="this.passwordTrue" id="input-group-5" label="Password Confirm:" label-for="input-5">
            <b-input
              id="input-6"
              required
              type='password'
              v-model="NewPasswords.confirmPassword"
              placeholder="Password Confirm"
            ></b-input>
          </b-form-group>
            <b-button v-if="this.passwordTrue"  @click="ChangePassword" class="ui button primary">Change password</b-button>
        </b-form>
      </b-modal>
    </div>
    </div>
    </div>
  </div>
</template>

<script>
import router from '../../router'
import axios from "axios"
export default {
  data(){
    return{
      newName: this.$store.getters.authUser.name,
      user: this.$store.getters.authUser,
      showInfo: true,
      showForm: false,
      show:true,
      showError:false,
      showNickname: false,
      passwordTrue: false,
      errorMassage: '',
      form:{
        password: '',
        email: ''
      },
      NewPasswords:{
        email: '',
        newPassword:  '',
        confirmPassword: "",
      }
    }

  },
  created() {
    if(!localStorage.getItem("user")){
      router.push("/")
    }
  },
  computed:{
    validationErrors(){
      if(this.$store.getters.errors){
        let errors = Object.values(this.$store.getters.errors);
        errors = errors.flat();
        return errors;
      }else{
        return null;
      }
    }
  },
  methods:{
      edit(){
        this.showInfo = false
      },
    ChangePassword(){
        this.NewPasswords.email = this.user.email
        if(this.NewPasswords.confirmPassword && this.NewPasswords.newPassword){
          if(this.NewPasswords.confirmPassword === this.NewPasswords.newPassword ){
              if( this.NewPasswords.newPassword !== this.form.password){
                    this.$store.dispatch("changePassword",this.NewPasswords)
              }else{
                this.errorMassage= "You can't use the old password"
              }
          }else{
            this.errorMassage= "password confirm is invalid"
          }
        }else{
          this.errorMassage= "new password is require"
        }
    },
    CheckPassword(){
      this.form.email = this.user.email
      axios.post("/auth/login",this.form).then((res) => {
        if(res.data.massage){
          this.errorMassage = "Please write a right password"
        }else{
          this.errorMassage = ''
          this.passwordTrue = true
        }

      })
    },
    onFile(evt){
      this.user.selectedFile = evt.target.files[0]
    },
    hideModal() {
      this.$refs['modal'].hide()

    },
    SignOut(){
      axios.post("/auth/logout")
        localStorage.removeItem("user")
        localStorage.removeItem("access_token")
      document.location.href = '/'

    },
    saveModal() {
      // We pass the ID of the button that we want to return focus to
      // when the modal has hidden

    },
    onSubmit(){

    },
    onSubmitProfile(evt) {
      // evt.preventDefault()
      // this.$refs['modal'].toggle('#toggle-btn')
        let formData = new FormData()
        formData.append('file', this.user.selectedFile)
        formData.append('name', this.newName)
        formData.append('email', this.user.email)
        formData.append('nickname', this.user.nickname)
      if(this.newName){
        this.user.name = this.newName
      }
       this.$store.dispatch("EditProfile",formData).then((res) =>{
         this.user.avatar = res.avatar
         if(!this.$store.getters.errors){
           this.$refs['modal'].toggle('#toggle-btn')
         }
       })


    },
    onReset(evt) {
      evt.preventDefault()
      // Reset our form values
      this.form.email = ''
      this.form.name = ''
      this.form.food = null
      this.form.checked = []
      // Trick to reset/clear native browser form validation state
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    }
  }
}
</script>

<style scoped>
.{

}
</style>
