class PaymentModel extends Fronty.Model {

  constructor( payerName, idProject, idPayment, totalAmount, subject, debtors) {
    super('PaymentModel'); //call super

    if (payerName) {
      this.payerName = payerName;
    }

    if (idProject) {
      this.idProject = idProject;
    }

    if (idPayment) {
      this.idPayment = idPayment;
    }

    if (totalAmount) {
      this.totalAmount = totalAmount;
    }

    if (subject) {
      this.subject = subject;
    }

    if (debtors) {
      this.debtors = debtors;
    }
  }

  setPayerName(payerName) {
    this.set((self) => {
      self.payerName = payerName;
    });
  }

  setIdProject(idProject) {
    this.set((self) => {
      self.idProject = idProject;
    });
  }

  setIdPayment(idPayment) {
    this.set((self) => {
      self.idPayment = idPayment;
    });
  }

  setTotalAmount(totalAmount) {
    this.set((self) => {
      self.totalAmount = totalAmount;
    });
  }

  setSubject(subject) {
    this.set((self) => {
      self.subject = subject;
    });
  }

  setDebtors(debtors) {
    this.set((self) => {
      self.debtors = debtors;
    });
  }
}
