function showMethodModal() {
  document.getElementById("methodForm").reset();
  document.getElementById("methodModal").classList.remove("hidden");
}

function hideMethodModal() {
  document.getElementById("methodModal").classList.add("hidden");
}

function showPaymentModal() {
  document.getElementById("paymentModal").classList.remove("hidden");
}

function hidePaymentModal() {
  document.getElementById("paymentModal").classList.add("hidden");
}

function printReceipt(client, method, amount, description, date) {
  const content = `
                <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body {
                            font-family: sans-serif;
                            padding: 20px;
                            background-color: #f9f9f9;
                        }
                        .receipt-box {
                            background: #fff;
                            padding: 20px;
                            border-radius: 8px;
                            border: 1px solid #ccc;
                            max-width: 500px;
                            margin: auto;
                        }
                        .receipt-header {
                            background-color: #16a34a; /* green-600 */
                            color: #ffffff;
                            padding: 15px;
                            border-top-left-radius: 8px;
                            border-top-right-radius: 8px;
                            display: flex;
                            align-items: center;
                            text-align: center;
                            justify-content: center;
                            gap: 15px;
                        }
                        .logo {
                            width: 50px;
                            height: 50px;
                            object-fit: contain;
                        }
                        .clinic-info {
                            display: flex;
                            flex-direction: column;
                            line-height: 1.4;
                        }
                        .clinic-name {
                            font-size: 20px;
                            font-weight: bold;
                            margin: 0;
                            color:rgb(0, 0, 0);
                        }
                        .clinic-address {
                            font-size: 14px;
                            margin: 0;
                            color:rgb(63, 61, 61);
                        }
                        .receipt-body {
                            padding-top: 15px;
                            margin-left: 20px;
                        }
                        h2 {
                            margin-top: 20px;
                            margin-bottom: 10px;
                        }
                        .info-line {
                            margin: 4px 0;
                        }
                        .footer {
                            margin-top: 20px;
                            border-top: 1px dashed #ccc;
                            padding-top: 10px;
                            text-align: center;
                        }
                        .pr {
                            padding-top: 10px;
                            text-align: center;
                        }
                    </style>
                </head>
                <body onload="window.print()">
                    <div class="receipt-box">
                        <div class="receipt-header">
                            <img src="image/MainIcon.png" alt="Vet Clinic Logo" class="logo">
                            <div class="clinic-info">
                                <p class="clinic-name">Balingasag Dog and Cat Clinic</p>
                                <p class="clinic-address">Cogon, Balingasag, Misamis Oriental</p>
                            </div>
                        </div>
                        <div class="pr">
                            <h3 class="pr">Payment Receipt</h3>
                        </div>
                        <div class="receipt-body">
                            <p class="info-line"><strong>Client:</strong> ${client}</p>
                            <p class="info-line"><strong>Payment Method:</strong> ${method}</p>
                            <p class="info-line"><strong>Amount:</strong> ₱${parseFloat(
                              amount
                            ).toFixed(2)}</p>
                            <p class="info-line"><strong>Description:</strong> ${description}</p>
                            <p class="info-line"><strong>Date:</strong> ${date}</p>
                        </div>
                        <div class="footer">
                            <p>Thank you for your payment!</p>
                        </div>
                    </div>
                </body>
                </html>
            `;

  const frame = document.getElementById("receiptFrame").contentWindow;
  frame.document.open();
  frame.document.write(content);
  frame.document.close();
}
