<template>
  <div>
    <el-row class="mb-20">
      <el-button @click="handleCreate">Add</el-button>
    </el-row>
    <div class="table-box card-base card-shadow--medium" v-loading="loading">
      <el-table :data="items" style="width: 100%">
        <el-table-column prop="id" label="#" width="80"></el-table-column>
        <el-table-column
          prop="name"
          label="Name"
          sortable
          width="400"
        ></el-table-column>
        <el-table-column prop="color" label="Color" width="700">
          <template slot-scope="scope">
            <div
              :style="`width:30px;height:30px;background-color:${scope.row.color}`"
            ></div>
          </template>
        </el-table-column>

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

      <el-dialog title="Info" :visible.sync="dialogVisible" v-loading="saving">
        <el-form :label-width="'120px'">
          <el-form-item label="Name"
            ><el-input v-model="item.name"></el-input
          ></el-form-item>
          <el-form-item label="Color"
            ><el-color-picker v-model="item.color"></el-color-picker
          ></el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">Close</el-button>
          <el-button v-if="item.id" @click="update()" type="primary"
            >Confirm</el-button
          >
          <el-button v-else @click="save()" type="primary">Confirm</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      item: {
        name: "",
        color: "#409EFF",
      },
      items: [],
      dialogVisible: false,
      action: "edit",
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
    handleDelete(id) {
      //TODO
    },
    update() {
      //TODO
    },
    save() {
      //TODO
    },
  },
};
</script>


