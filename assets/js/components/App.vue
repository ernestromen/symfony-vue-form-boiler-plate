<template>
  <div
    class="alert alert-danger w-50 m-auto"
    role="alert"
    :style="{ display: responseNetworkError ? 'block' : 'none' }"
  >
    {{ responseNetworkErrorMessage }}
  </div>
  <form
    @submit.prevent="addPost"
    method="post"
    action="http://localhost:8000/addpost"
  >
    <div class="mb-3 w-50 m-auto">
      <label for="post" class="form-label">Post</label>
      <textarea
        id="post"
        name="post"
        class="form-control"
        rows="4"
        v-model="postTextArea"
      ></textarea>
    </div>
    <div class="d-flex mt-2">
      <input type="submit" class="btn btn-primary m-auto" value="Submit" />
    </div>
  </form>

  <table class="table w-50 text-center m-auto border">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Blog</th>
        <th scope="col">Created at</th>
      </tr>
    </thead>
    <tbody>
      <tr class="border" v-for="post in posts" :key="post.id">
        <td>{{ post.id }}</td>
        <td>{{ post.post }}</td>
        <td>{{ post.CreatedAt.date.split(".")[0] }}</td>
        <td>
          <a
            class="btn btn-success"
            :href="'http://localhost:8000/edit/' + post.id"
            >Edit post</a
          >
        </td>
        <td>
          <button class="btn btn-danger" @click="deletePost(post.id)">
            Delete post
          </button>
        </td>
      </tr>
      <tr class="border border-info"></tr>
    </tbody>
  </table>
</template>

<script>
import axios from "axios";
import moment from "moment";

export default {
  data: function () {
    return {
      posts: [],
      postTextArea: "",
      responseNetworkError: false,
      responseNetworkErrorMessage: "",
    };
  },
  methods: {
    getAllPosts: function () {
      axios
        .get("http://localhost:8000/allposts")
        .then((response) => {
          this.posts = response.data;
        })
        .catch((error) => {
          this.errorHandler(error);
        });
    },
    addPost: function (e) {
      e.preventDefault();
      axios
        .post("http://localhost:8000/addpost", {
          data: this.postTextArea,
        })
        .then((response) => {
          let newPost = {
            id: response.data.lastAddedPostId,
            post: this.postTextArea,
            CreatedAt: { date: this.currentDateTime },
          };

          this.posts = [...this.posts, newPost];
        })
        .catch((error) => {
          this.errorHandler(error);
        });
    },
    deletePost: function (id) {
      axios
        .delete(`http://localhost:8000/delete/${id}`)
        .then((response) => {
          let fillteredPosts = this.posts.filter((e) => e.id !== id);
          this.posts = fillteredPosts;
        })
        .catch((error) => {
          this.errorHandler(error);
        });
    },
    showFailedApiMessage: function (error) {
      switch (error["response"]["status"]) {
        case 404:
          return "No posts exist, please add something!";
          break;
        default:
          return `${error["message"]} : ${error["name"]} `;
      }
    },
    errorHandler: function (error) {
      this.responseNetworkErrorMessage = this.showFailedApiMessage(error);
      this.responseNetworkError = true;

      setTimeout(() => {
        this.responseNetworkError = false;
        this.responseNetworkErrorMessage = "";
      }, 2500);
    },
  },
  mounted() {
    this.getAllPosts();
    this.currentDateTime = moment().format("YYYY-MM-DD HH:mm:ss");
  },
};
</script>
