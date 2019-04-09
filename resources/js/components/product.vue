<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h2>CRUD</h2>
                        <button type="button" class="btn btn-success"  data-toggle="modal" data-target="#productModal">Add Product</button>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td>Title</td>
                                    <td>Discription</td>
                                    <td>Photo</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <!-- Modal -->
                    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form @submit.prevent="create">
                                    <div class="modal-body ">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input v-model="form.title" type="text" name="title"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                            <has-error :form="form" field="title"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <input v-model="form.discription" type="number" name="discription"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('discription') }">
                                            <has-error :form="form" field="discription"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label>Photo</label>
                                            <input type="file"  name="photo"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('photo') }">
                                            <has-error :form="form" field="photo"></has-error>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- set progressbar -->
        <vue-progress-bar></vue-progress-bar>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: new Form({
                    title: '',
                    discription: '',
                    photo: '',
                })
            }
        },

        methods: {
            create() {
                this.$Progress.start()
                this.form.post('/product')
                .then(() => {
                    $('#productModal').modal('hide')
                    Toast.fire({
                        type: 'success',
                        title: 'Add product Successfully'
                    });
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                });
            },
        },

        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
