<template>
  <div class="scrollable" v-loading="loading || saving">
    <h2>{{ ticket.subject }}</h2>

    <el-card
      v-for="comment in comments"
      :key="comment.id"
      class="ticket-post-card mb-20"
      style="background-color: cornsilk"
    >
      <div slot="header">
        <el-button
          v-if="user.id == comment.user_id"
          style="float: right"
          size="mini"
          icon="el-icon-remove"
          type="danger"
          @click="deleteComment(comment.id)"
        >
        </el-button>
        <div>
          <span>{{ comment.user.name }}</span>
        </div>
        <div style="float: right">
          {{ comment.created_at }}
        </div>
      </div>
      <div class="message" v-html="comment.message"></div>
    </el-card>

    <el-card
      v-for="post in posts"
      :key="post.id"
      class="ticket-post-card mb-20"
    >
      <div slot="header">
        <div>
          <i
            :style="{ color: post.user_role === 'admin' ? '#5f8fdf' : 'grey' }"
            class="el-icon-user-solid"
          ></i>
          <span>{{ post.user.email }}</span>
        </div>
        <div style="float: right">{{ post.created_at }}</div>
      </div>
      <div class="message" v-html="post.message"></div>
      <div class="post-attachments">
        <div v-for="file in post.uploads" class="mb-5" :key="file.id">
          <a
            target="_blank"
            :href="`/tickets/${ticket.id}/${file.id}/download`"
            >{{ file.path.split("/").pop() }}</a
          >
        </div>
      </div>
    </el-card>

    <ticket-form
      @save="saveTicketPost"
      @cancel="back()"
      class="mb-20"
    ></ticket-form>
  </div>
</template>

<script>
import TicketForm from "./TicketForm";
export default {
  data() {
    return {
      ticket: {},
      posts: [],
      comments: [],
    };
  },
  components: {
    TicketForm,
  },
  computed: {
    user() {
      //Return current use logged in
    },
    loading() {
      //You can set variable to use loading
    },
    saving() {
      //You can set variable to use saving process
    },
    currentLocation() {
      return window.location.pathname;
    },
  },
  mounted() {
    this.get();
  },
  methods: {
    deleteComment(commentID) {
      //TODO
    },
    get() {
      //TODO
    },
    saveTicketPost(form) {
      //TODO
    },
    back() {
      this.$router.back();
    },
  },
};
</script>


