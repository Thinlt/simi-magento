import { connect } from "@magento/venia-drivers";
import ProductFullDetail from "./ProductFullDetail";

const mapStateToProps = ({
  cart
}) => {
  return {
    isAddingItem: cart.isAddingItem
  };
};

export default connect(mapStateToProps)(ProductFullDetail);
//# sourceMappingURL=container.js.map