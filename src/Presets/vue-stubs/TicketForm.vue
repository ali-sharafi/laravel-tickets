<template>
  <div>
    <el-card>
      <el-form label-position="top">
        <el-row>
          <el-col :span="12">
            <el-form-item label="Message">
              <el-input type="textarea" v-model="message"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Comment" class="ml-10">
              <el-input type="textarea" v-model="comment"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
    </el-card>

    <el-card>
      <el-upload
        class="upload-demo"
        ref="upload"
        :multiple="true"
        action=""
        :auto-upload="false"
        :on-change="handleChangeAttachments"
      >
        <el-button slot="trigger" size="small" type="primary"
          >Choose File</el-button
        >
        <div slot="tip" class="el-upload__tip">
          Allowed File Extensions: .jpg, .gif, .jpeg, .png, .txt, .pdf with a
          size less than 2000Kb
        </div>
      </el-upload>
    </el-card>

    <el-checkbox class="mt-10" v-model="close_ticket">Close ticket</el-checkbox>

    <el-row type="flex" justify="start" class="mt-20">
      <el-button @click="savePost" class="ph-30 mr-20">Submit</el-button>
      <el-button @click="cancel" class="ph-30">Cancel</el-button>
    </el-row>
  </div>
</template>

<script>
export default {
  name: "ticket_post_form",
  data() {
    return {
      message: "",
      comment: "",
      files_max: 5,
      close_ticket: false,
    };
  },
  methods: {
    handleChangeAttachments(file) {
      let f = file.raw;
      const isImage =
        [
          "image/jpeg",
          "image/png",
          "image/gif",
          "application/pdf",
          "text/plain",
        ].indexOf(f.type) !== -1;
      const isLt2K = f.size / 1024 < 2000;

      if (this.$refs.upload.uploadFiles.length > this.files_max) {
        //TODO: Show some error message like:"Maximum number of files: " + this.files_max
      }
      if (!isImage) {
        //TODO: Show some error message like:Allowed File Extensions: .jpg, .gif, .jpeg, .png, .txt, .pdf
      }
      if (!isLt2K) {
        //TODO: Show some error message like:"File size can not exceed 2Kb"
      }
      if (
        !(
          isImage &&
          isLt2K &&
          this.$refs.upload.uploadFiles.length <= this.files_max
        )
      ) {
        this.$refs.upload.uploadFiles = this.$refs.upload.uploadFiles.filter(
          (i) => {
            return i.uid !== file.uid;
          }
        );
      }
    },
    savePost() {
      let data = {
        message: this.message,
        comment: this.comment,
        close_ticket: this.close_ticket,
        files: this.$refs.upload.uploadFiles,
      };
      this.$emit("save", data);
      this.clear();
    },
    cancel() {
      this.$emit("cancel");
      this.clear();
    },
    clear() {
      this.message = "";
      this.comment = "";
      this.$refs.upload.uploadFiles = [];
    },
  },
};
</script>