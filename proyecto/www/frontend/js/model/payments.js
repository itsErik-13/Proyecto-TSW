class PaymentsModel extends Fronty.Model {

    constructor() {
      super('PaymentsModel'); //call super
  
      // model attributes
      this.payments = [];
    }
      
    setPayments(payments) {
      this.set((self) => {
        self.payments = payments;
      });
    }
  }
  