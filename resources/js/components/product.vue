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
                                    <th>Title</th>
                                    <th>Discription</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in allproducts" :key='product.id'>
                                    <td>{{ product.title }}</td>
                                    <td>{{ product.description }}</td>
                                    <td>
                                         <img width="100" :src="showphoto(product.photo)" alt="">   
                                    </td>
                                    <td style="font-size: 25px">
                                        <a href="http://">
                                            <i class="far fa-edit text-info"></i>
                                        </a>/
                                        <a href="http://">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                    
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
                                            <textarea  v-model="form.description" type="text" name="description"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                            <has-error :form="form" field="description"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label>Photo</label>
                                            <input type="file"  name="photo" @change="onphoto"
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
                allproducts: {},
                form: new Form({
                    id: '',
                    title: '',
                    description: '',
                    photo: '',
                })
            }
        },

        methods: {
            showphoto(naem) {
                return "uploads/product/" + naem;
            },
            load() {
                axios.get('all/product')
                .then( ({data}) => (this.allproducts = data.data));
            },
            onphoto (e) {
                let type = ['image/jpe', 'image/jpeg', 'image/png', 'image/gif']
                let file = e.target.files[0];
                let reader = new FileReader();
                if (type.includes(file['type'])) {
                    if (file['size'] <= 2097152) {
                        reader.onloadend = (file) => {
                           this.form.photo = reader.result; 
                        }
                        reader.readAsDataURL(file);
                    } else {
                        this.form.photo = ''; 
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'The maximum size of image size is 2 MB',
                        })
                    }
                } else {
                    this.form.photo = ''; 
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'The image must be JPG JPEG PNG GIF ',
                    })  
                }

            },
            create() {
                this.$Progress.start();
                this.form.post('/product')
                .then(() => {
                    $('#productModal').modal('hide');
                    Toast.fire({
                        type: 'success',
                        title: 'Coupon Created Successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
        },

        mounted() {
            this.load();
        }
    }
</script>
