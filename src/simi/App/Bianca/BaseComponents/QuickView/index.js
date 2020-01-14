import React from 'react'
import Modal from 'react-responsive-modal'
import Identify from 'src/simi/Helper/Identify'
import connectorGetProductDetailByUrl from 'src/simi/queries/catalog/getProductDetail.graphql';
import connectorGetProductDetailBySku from 'src/simi/queries/catalog/getProductDetailBySku.graphql'
import { Simiquery } from 'src/simi/Network/Query' 
import { saveDataToUrl, productUrlSuffix } from 'src/simi/Helper/Url';
import Loading from 'src/simi/BaseComponents/Loading';
import ProductFullDetail from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail';
require('./styles.scss');


class QuickView extends React.Component {
    constructor(props) {
        super(props)
    }

    onCloseModal = () => {
        this.props.closeModal()
    }

    render() {
        const { openModal, product } = this.props
        const dataModal = (props) => {
            const {preloadedData} = props
            if (preloadedData && !preloadedData.is_dummy_data) { //saved api is full api, then no need api getting anymore
                return (
                    <QuickViewDetail
                        product={preloadedData}
                    />
                )
            }
            const sku = Identify.findGetParameter('sku') //cases with url like: product.html?sku=ab12
            const productQuery = sku ? connectorGetProductDetailBySku : connectorGetProductDetailByUrl
            const variables = { onServer: false }
            if (sku)
                variables.sku = sku
            else
                variables.urlKey = props.url_key

            return (
                <Simiquery
                    query={productQuery}
                    variables={variables}
                    fetchPolicy="no-cache" //cannot save to cache cause of "heuristic fragment matching" from ConfigurableProduct and GroupedProduct
                >
                    {({ error, data }) => {
                        if (error) return <div>{Identify.__('Data Fetch Error')}</div>;
                        let product = null

                        if (data && data.productDetail && data.productDetail.items && !data.productDetail.items.length) {
                            return ''
                        }
                        if (data && data.productDetail && data.productDetail.items && data.productDetail.items.length) {
                            //prepare data
                            product = data.productDetail.items[0];
                            let simiExtraField = data.simiProductDetailExtraField
                            simiExtraField = simiExtraField ? JSON.parse(simiExtraField) : null
                            product.simiExtraField = simiExtraField
                            //save full data to quote
                            if (product.url_key)
                                saveDataToUrl(`/${product.url_key}${productUrlSuffix()}`, product, false)
                        } else if (preloadedData) {
                            product = preloadedData
                        }
                        if (product) {
                            return (
                                <ProductFullDetail
                                    product={product} hideRelatedProduct={true}
                                />
                            );
                        }
                        return <Loading />
                    }}
                </Simiquery>
            );
        }
        return (
            <Modal modalId="modal-quick-view" overlayId="modal-quick-view-overlay"
            open={openModal} onClose={this.onCloseModal}
            classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
            >
                <div className="modal-quick-view-inner">
                    {dataModal(product)}
                </div>
            </Modal>
        )
    }
}

export default QuickView