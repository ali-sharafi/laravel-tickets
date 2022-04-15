<template>
  <div>
    <el-row class="mb-20">
      <el-button @click="handleCreate">Add</el-button>
    </el-row>
    <div class="table-box card-base card-shadow--medium" v-loading="loading">
      <el-table :data="items" style="width: 100%" v-loading="loading">
        <el-table-column prop="id" label="#" width="80"></el-table-column>
        <el-table-column
          prop="title"
          label="Title"
          sortable
          width="400"
        ></el-table-column>
        <el-table-column
          prop="desc"
          label="Description"
          width="700"
        ></el-table-column>

        <el-table-column label="Actions" width="260">
          <template slot-scope="scope">
            <el-button
              size="mini"
              icon="el-icon-edit"
              type="danger"
              @click="handleEdit(scope.row)"
            >
            </el-button>
            <el-button
              size="mini"
              icon="el-icon-remove"
              type="danger"
              @click="handleDelete(scope.row.id)"
            >
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-dialog title="Info" :visible.sync="dialogVisible">
        <el-form :label-width="'120px'">
          <el-form-item label="Title"
            ><el-input v-model="item.title"></el-input
          ></el-form-item>
          <el-form-item label="Description"
            ><el-input v-model="item.desc"></el-input
          ></el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">Close</el-button>
          <el-button @click="save()" type="primary">Confirm</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      item: {},
      items: [],
      dialogVisible: false,
      action: "create",
    };
  },
  computed: {
    loading() {
      //You can set variable to use loading
    },
    saving() {
      //You can set variable to use saving process
    },
    dialogTitle() {
      return this.action === "create" ? "Create" : "Edit";
    },
  },
  mounted() {
    this.get();
  },
  methods: {
    handleDelete(categotyID) {
      //TODo
    },
    get() {
      //TODO
    },
    handleEdit(item) {
      this.item = { ...item };
      this.action = "edit";
      this.dialogVisible = true;
    },
    handleCreate() {
      this.item = {};
      this.action = "create";
      this.dialogVisible = true;
    },
    save() {
      //TODO
    },
  },
};
</script>


