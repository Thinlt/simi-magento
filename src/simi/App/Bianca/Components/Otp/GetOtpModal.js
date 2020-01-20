import React from 'react'
import Modal from 'react-responsive-modal'
import Identify from 'src/simi/Helper/Identify'
require('./getOtpModal.scss');

class GetOtpModal extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {

        const { openGetModal, closeGetModal } = this.props

        return (
            <Modal
                open={openGetModal} onClose={closeGetModal}
            >

            </Modal>
        )
    }
}

export default GetOtpModal