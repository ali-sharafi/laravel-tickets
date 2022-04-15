<template>
  <div>
    <div class="table-box card-base card-shadow--medium" v-loading="loading">
      <el-row>
        <el-col :span="4">
          <el-select
            clearable
            @change="get"
            class="p-10"
            v-model="filters.label_id"
            placeholder="Label"
          >
            <el-option
              v-for="label in labels"
              :key="'s' + label.id"
              :label="label.name"
              :value="label.id"
            >
              <span style="float: left">{{ label.name }}</span>
              <span
                :style="`float: right;width: 30px;height: 30px; font-size: 13px;background-color: ${label.color};`"
              ></span>
            </el-option>
          </el-select>
        </el-col>
        <el-col :span="4">
          <el-select
            clearable
            @change="get"
            class="p-10"
            v-model="filters.category_id"
            placeholder="Category"
          >
            <el-option
              v-for="category in categories"
              :key="category.id"
              :label="category.title"
              :value="category.id"
            >
            </el-option>
          </el-select>
        </el-col>
      </el-row>
      <el-table :data="items" style="width: 100%" v-loading="loading">
        <el-table-column prop="id" label="#" width="50"></el-table-column>
        <el-table-column label="Actions" width="230">
          <template slot-scope="scope">
            <el-button
              v-if="scope.row.state !== 'CLOSED'"
              size="mini"
              icon="el-icon-edit"
              type="danger"
              @click="to('/tickets/' + scope.row.id + '/reply')"
            >
            </el-button>
            <el-button
              v-if="scope.row.state !== 'CLOSED'"
              size="mini"
              icon="el-icon-close"
              type="danger"
              @click="close(scope.row.id)"
            >
            </el-button>
            <el-button
              v-else
              size="mini"
              type="danger"
              @click="reopen(scope.row.id)"
              >Reopen
            </el-button>
            <el-button
              v-if="scope.row.state !== 'CLOSED'"
              size="mini"
              type="danger"
              @click="handleReassign(scope.row)"
              >Re-Assign
            </el-button>
          </template>
        </el-table-column>
        <el-table-column
          prop="user.email"
          label="User"
          sortable
          width="180"
        ></el-table-column>
        <el-table-column
          prop="subject"
          label="Title"
          width="200"
        ></el-table-column>
        <el-table-column
          prop="agent.name"
          label="Agent"
          sortable
          width="150"
        ></el-table-column>
        <el-table-column
          prop="category_name"
          label="Category"
          sortable
          width="150"
        ></el-table-column>
        <el-table-column prop="label" label="Label" width="60">
          <template slot-scope="scope">
            <div
              v-if="scope.row.label"
              :style="`width:30px;height:30px;background-color:${scope.row.label.color}`"
            ></div>
            <div v-else></div>
          </template>
        </el-table-column>
        <el-table-column
          prop="state"
          label="Status"
          sortable
          width="100"
        ></el-table-column>
      </el-table>
    </div>

    <div class="block mt-20">
      <el-pagination
        @size-change="handleSizePageChange"
        @current-change="handlePageChange"
        :current-page.sync="pagination.currentPage"
        :page-sizes="pagination.pageSizes"
        :page-size="pagination.pageSize"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pagination.total"
      >
      </el-pagination>
    </div>

    <el-dialog
      title="Re-Assign"
      :visible.sync="dialogVisible"
      v-loading="saving"
    >
      <el-form :label-width="'120px'">
        <el-form-item label="Category">
          <el-select v-model="item.category_id">
            <el-option
              v-for="category in categories"
              :key="'s' + category.id"
              :label="category.title"
              :value="category.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Label">
          <el-select v-model="item.label_id">
            <el-option
              v-for="label in labels"
              :key="'s' + label.id"
              :label="label.name"
              :value="label.id"
            >
              <span style="float: left">{{ label.name }}</span>
              <span
                :style="`float: right;width: 30px;height: 30px; font-size: 13px;background-color: ${label.color};`"
              ></span>
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">Close</el-button>
        <el-button @click="reassign()" type="primary">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      item: {},
      items: [],
      filters: {
        label_id: "",
        category_id: "",
      },
      categories: [],
      labels: [],
      dialogVisible: false,
      action: "edit",
      pagination: {
        currentPage: 1,
        total: null,
        pageSize: 20,
        pageSizes: [10, 20, 50, 100],
      },
    };
  },
  computed: {
    loading() {
      //You can set variable to use loading
    },
    saving() {
      //You can set variable to use saving process
    },
  },
  watch: {
    "$route.params.status": function () {
      this.get();
    },
  },
  mounted() {
    this.getCategories();
    this.getLabels();
    this.get();
  },
  methods: {
    to(path) {
      this.$router.push(path);
    },
    get() {
      //TODO - don't forget to pass filters
    },
    getCategories() {
      //TODO
    },
    getLabels() {
      //TODO
    },
    save() {
      //TODO
    },
    close(id) {
      //TODO
    },
    reopen(id) {
      //TODO
    },
    reassign() {
      //TODO
    },
    handleReassign(item) {
      this.item = {
        id: item.id,
        category_id: String(item.category_id),
        label_id: item.label_id,
      };
      this.dialogVisible = true;
    },

    handleSizePageChange(val) {
      this.pagination.pageSize = val;
      this.get();
    },
    handlePageChange(val) {
      this.pagination.currentPage = val;
      this.get();
    },
  },
};
</script>


