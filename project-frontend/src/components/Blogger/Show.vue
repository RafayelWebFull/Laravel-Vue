<template>
  <main class="py-5">
    <div class="container">
      <div class="row justify-content-md-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header card-title">
              <strong>Contact Details</strong>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label  class="col-md-3 col-form-label">Post Title</label>
                    <div class="col-md-9">
                      <p class="form-control-plaintext text-muted">{{ post.title}}</p>
                    </div>
                  </div>

                  <div class="form-group row mb-5">
                    <label class="col-md-3 col-form-label">Post Image</label>
                    <div style="height: 50px;" >
                         <img width="80px"  :src="`http://task.loc/storage/postsPhoto/${post.image}`" alt="">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label  class="col-md-3 col-form-label">Post Author</label>
                    <div class="col-md-9">
                      <p class="form-control-plaintext text-muted">{{post.author}}</p>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Description</label>
                    <div class="col-md-9">
                      <p class="form-control-plaintext text-muted">{{post.description}}</p>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Category Name</label>
                    <div class="col-md-9">
                      <p class="form-control-plaintext text-muted">{{post.category.category_name}}</p>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Status</label>
                    <div class="col-md-9">
                      <p class="form-control-plaintext text-muted">{{post.status}}</p>
                    </div>
                  </div>
                  <div v-if="post.status_created" class="form-group row">
                    <label class="col-md-3 col-form-label">Block Status</label>
                    <div class="col-md-9">
                      <p class="form-control-plaintext text-muted">{{post.status_created}}</p>
                    </div>
                  </div>
                  <div class="comment rounded mb-1 border"  v-for="(comment,index) in post.comments">
                    <div class="d-inline">
                      <div style="width: 300px">
                        <h6 class="d-block">{{index  +  1 + "." + comment.comment_author }}</h6>
                      </div>
                      <p class="ml-3 text-success">{{comment.comment}}</p>
                      <div class=" ml-4 align-content-center p-2" v-if="comment_reply.comment_id === comment.id" v-for="comment_reply in comment_reply">
                        <h5>{{comment_reply.comment_reply_author}}</h5>
                        <p class="text-danger ml-2 font-weight-bold">{{comment_reply.comment_reply}}</p>
                      </div>
                      <form  @submit.prevent="onReply"  class="p-2 d-flex">
                        <b-input class="mt-1 ml-3 " type="text" :data-title="user.name" :data-id="post.id" :data-comment-id="comment.id"  placeholder="Comment Reply"></b-input>
                        <b-button size="sm" type="submit" class="ml-1">Reply</b-button>
                      </form>
                    </div>
                  </div>
                  <div>
                    <form  @submit.prevent="onSubmit"  class="d-flex">
                      <b-form-input placeholder="Comment" :data-post-id="post.id" :data-title="post.name"  v-model="comments.comment"  type="text" ></b-form-input>
                      <b-button  type="submit" class="ml-1">Comment</b-button>
                    </form>
                  </div>
                  <hr>
                  <div class="form-group row mb-0">
                    <div class="col-md-9 ">
                      <router-link :to="{name: 'BloggerIndex'}"><a  class="btn btn-info">Cancel</a></router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  data(){
    return{
      comments:{
        comment: '',
        post_id: '',
        author: '',
        id: ''
      },
      comment_replies:{
        comment_reply: '',
        comment_id : '',
        comment_reply_author: '',
        post_id: '',
      }
    }
  },
  methods:{
    onSubmit(evt){
      evt.target[0].value= ""
      this.comments.post_id = evt.target[0].getAttribute("data-post-id")
      this.comments.author = this.user.name
      this.$store.dispatch("commentPost",this.comments)
    },
    onReply(evt){
      this.comment_replies.comment_reply = evt.target[0].value
      evt.target[0].value= ""
      this.comment_replies.comment_id = evt.target[0].getAttribute("data-comment-id")
      this.comment_replies.comment_reply_author = this.user.name
      this.comment_replies.post_id=evt.target[0].getAttribute("data-id")
      this.$store.dispatch("commentReply",this.comment_replies)
      // this.comments.id = evt.target[0].getAttribute("data-id")
    },
  },
  computed:{
    post(){
      return this.$store.getters.post
    },
    comment_reply(){
      return this.$store.getters.CommentsReply
    },
    user(){
      return this.$store.getters.authUser
    }
  }
}
</script>

<style scoped>

</style>
